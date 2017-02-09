<div id="main_sent_message">
    <div  class="heading col1">
        <h1 class="mainsubsheading">
          <?php if ($uertype=='Psychologist'): ?>
            <?=$user['first_name'].' '.$user['last_name']?>
          <?php else: ?>
            <?=lang('txt_manage_app')?>
          <?php endif ?>
        </h1>
    </div>
<div id ="contentArea" class="row clear">
<div id="iframediv">
  <iframe id="frame" src="" width="100%" height="307"> </iframe>
</div>
<?php $this->load->view('minapp/minapp/comment_ajax',$data); ?>
 <a href="<?php echo base_url().'index.php/minapp/view/'.$user['username']; ?>" class="btnMidall marginaltl"> Tillbaka</a>
</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
  var printurl = $baseUrl + "assets/app?json=";
  var task_id = "<?=$task_id?>";
  var user_id = "<?=$user['id']?>";
  var total = "<?=$training->total?>";

  $('#iframediv').hide();
    if (total > 0) {
      $.ajax({
        type: 'post',
        url: $sitePath + "/minapp/minapp/getGraphInput",
        dataType: 'json',
        cache: false,
        data: {
          task_id: task_id,
          user_id: user_id
        },
        success: function(response) {
          $str_json = JSON.stringify(response);
          $url = printurl + encodeURIComponent($str_json);
          $('#iframediv').show();
          $("#frame").attr("src", $url); }
      });
    }

});
</script>

