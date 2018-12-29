<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');


// Initialize search variables.
$id = null;
$search_categories = null;
$search_type = null;
$search_grades = null;
$search_keywords = null;
$order_by = "new";
$page = 0;

// Settings
define("ITEMS_PER_PAGE", 25);

// Check the URL for search arguments.
if (isset($_GET['id'])) {
  $id = $_GET['id'];
}

if (isset($_GET['categories'])) {
  if ($_GET['categories'] !== "-1") {
    $search_categories = $_GET['categories'];
  }
}

if (isset($_GET['order_by'])) {
  $order_by = $_GET['order_by'];
}

if (isset($_GET['type'])) {
  $search_type = $_GET['type'];
}

if (isset($_GET['grades'])) {
  $search_grades = $_GET['grades'];
}

if (isset($_GET['keywords'])) {
  $search_keywords = $_GET['keywords'];
}

if (isset($_GET['page'])) {
  $page = (int) $_GET['page'] - 1;
}

// Setting up the page.
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title("WCLN: LOR");
$PAGE->set_heading("WCLN Learning Material");
$PAGE->set_url(new moodle_url('/local/lor/index.php'));

// Require Javascript and CSS files.
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url("https://bclearningnetwork.com/lib/bootstrap/bootstrap.min.js"));
$PAGE->requires->css(new moodle_url("lib/multiple-select/multiple-select.css"));
$PAGE->requires->js(new moodle_url("js/navigation.js"));
$PAGE->requires->js(new moodle_url("js/modal_handler.js"));

// Configuring the Nav bar.
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('lor', 'local_lor'), new moodle_url('/local/lor/index.php'));

// Ouput the header.
echo $OUTPUT->header();

// Call locallib functions to set variables.
$content = local_lor_get_content($search_type, $search_categories, $search_grades, $order_by, $search_keywords);
$categories = local_lor_get_categories();
$types = local_lor_get_types();
$grades = local_lor_get_grades();
$number_of_pages = ceil(count($content) / ITEMS_PER_PAGE);

?>

<div class="container-fluid bootstrap-lor" id="content-container">
  <!-- Filter settings -->
  <div class="row-fluid">
    <div class="col-md-12" id="filters">
      <form action="index.php" method="GET">

        <div class="filter">
          <label>Type:</label>
          <select class="lor-select" name="type" id="type-select">
            <option value="-1">All Types</option>
            <?php foreach($types as $type): ?>
              <?php if ($type->id == $search_type): ?>
                <option selected="selected" value="<?=$type->id?>"><?=$type->name?></option>
              <?php else: ?>
                <option value="<?=$type->id?>"><?=$type->name?></option>
              <?php endif ?>
            <?php endforeach ?>
          </select>
        </div>

        <div class="filter">
          <label>Categories:</label>
          <select name="categories[]" class="multiple lor-select" multiple="multiple">
          <?php foreach ($categories as $category): ?>
            <?php if (in_array($category->id, $search_categories)): ?>
              <option selected="selected" value="<?=$category->id?>"><?=$category->name?></option>
            <?php else: ?>
              <?php if (is_null($search_categories)): ?>
                <option selected="selected" value="<?=$category->id?>"><?=$category->name?></option>
              <?php else: ?>
                <option value="<?=$category->id?>"><?=$category->name?></option>
              <?php endif?>
            <?php endif ?>
          <?php endforeach ?>
          </select>
        </div>

        <div class="filter">
          <label>Grades:</label>
          <select name="grades[]" class="multiple lor-select" multiple="multiple">
            <?php foreach ($grades as $grade): ?>
              <?php if (in_array($grade->grade, $search_grades)): ?>
                <option selected="selected" value="<?=$grade->grade?>"><?=$grade->grade?></option>
              <?php else: ?>
                <?php if (is_null($search_categories)): ?>
                  <option selected="selected" value="<?=$grade->grade?>"><?=$grade->grade?></option>
                <?php else: ?>
                  <option value="<?=$grade->grade?>"><?=$grade->grade?></option>
                <?php endif ?>
              <?php endif ?>
            <?php endforeach ?>
          </select>
        </div>

        <div class="filter">
          <label>Sort by:</label>
          <select class="lor-select" name="order_by">
            <option value="new">Recently Added</option>
            <?php if ($order_by === "alphabetical"): ?>
              <option value="alphabetical" selected="selected">Alphabetical</option>
            <?php else: ?>
              <option value="alphabetical">Alphabetical</option>
            <?php endif ?>
          </select>
        </div>

        <div class="filter">
          <input type="text" placeholder="Keywords..." name="keywords" value="<?=$search_keywords?>">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
    </div>
  </div>

<!-- Modal template to be rendered by click. -->
<div class="lor-modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
  <div class="lor-modal-dialog lor-modal-xl">
    <div class="lor-modal-content">
      <div class="lor-modal-header">
        <h4 class="lor-modal-title col-12 text-center" id="myModalLabel"><!-- To be replaced by jquery modal call. --></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="lor-modal-body">
        <!-- To be replaced by jquery modal call. -->
      </div>
      <div class="lor-modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


  <!-- Content -->
  <div class="row-fluid text-center">
    <div class="col-md-12 text-center">
      <p id="count"><i><?=count($content)?> items match your search</i></p>
    </div>
    <?php if (!is_null($content)): ?>
      <?php for ($i = $page * ITEMS_PER_PAGE; $i < (($page + 1) * ITEMS_PER_PAGE); $i++) { ?>
        <?php if ($i === count($content)) break; ?>
        <?php $item = array_values($content)[$i]; ?>
      </div>
      <div class="row text-center item">
      <div class="col-md-4 item-image">
        <a class="modallink" href="modals/details.php?id=<?=$item->id?>" data-remote="false" data-target="#itemModal">
          <img class="lor-image" src="<?=$item->image?>" width="200px" height="150px" />
        </a>

      </div>
      <div class="col-md-7 project-about text-left">
        <a class="modallink" href="modals/details.php?id=<?=$item->id?>" data-remote="false" data-target="#itemModal">
          <h3><?=$item->title?></h3>
        </a>
        <p><i>Topics: </i><?=local_lor_get_keywords_string_for_item($item->id)?></p>
        <p>
          <a href="modals/details.php?id=<?=$item->id?>" data-remote="false" data-target="#itemModal" class="modallink lor-icon">
            <img src="images/icons/details.png">Details
          </a>
          <a href="modals/related.php?id=<?=$item->id?>" data-remote="false" data-target="#itemModal" class="modallink lor-icon">
            <img src="images/icons/related.png">Related
          </a>
          <a class="lor-icon" href="<?=$item->link?>" target="_blank">
            <img src="images/icons/present.png">Present
          </a>
          <a href="modals/share.php?id=<?=$item->id?>"  data-remote="false" data-target="#itemModal" class="modallink lor-icon">
            <img src="images/icons/share.png">Share
          </a>
          <a href="modals/embed.php?id=<?=$item->id?>"  data-remote="false" data-target="#itemModal" class="modallink lor-icon">
            <img src="images/icons/embed.png">Embed
          </a>
        </p>
      </div>

    <?php } ?>
    <?php endif ?>
</div>

<?php if (!is_null($content) && count($content) > 0): ?>
  <div class="row-fluid text-center" id="pages-row">
    <div class="col-md-2" id="previous-page">
      <?php if ($page !== 0): ?>
        <a class="back round lor-nav" id="backBtn" onclick="back(<?=$page+1?>)">&#8249;</a>
      <?php endif ?>
    </div>
    <div class="col-md-8" id="pages">
      <p>Page:
        <?php for ($i = 1; $i <= $number_of_pages; $i++) { ?>
          <?php if ($i === $page + 1): ?>
            <a class="page" id="current-page"><?=$i?></a>
          <?php else: ?>
            <a onclick="navigate(<?=$i?>)" class="page"><?=$i?></a>
          <?php endif ?>
        <?php } ?>
      </p>
    </div>
    <div class="col-md-2" id="next-page">
      <?php if ($page != ($number_of_pages - 1)): ?>
        <a class="next round lor-nav" id="nextBtn" onclick="next(<?=$page+1?>)">&#8250;</a>
      <?php endif ?>
    </div>
  </div>
<?php endif ?>
<script src="lib/multiple-select/multiple-select.js"></script>
<script>
    $('.multiple').multipleSelect();
</script>
</div>

<?php
echo $OUTPUT->footer();
?>
