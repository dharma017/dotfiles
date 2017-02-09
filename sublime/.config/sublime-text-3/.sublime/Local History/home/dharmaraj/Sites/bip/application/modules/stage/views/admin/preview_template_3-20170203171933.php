<?php
$this->load->view("stage/admin/template_header.php");
?>

<div id ="contentArea" class="ptemp3 pad10 row clear previewTemplate3">
    <?php if ($description)
        echo '<div class="clear margin_bottom wrapper600">' . $description . '</div>'; ?>
    <div id="leftpanel" class="col1">
        <div class="gallery">
            <?php
            $nm = 0;
            foreach ($templateData as $linkdata) {
                $totallink = count($templateData);
                if ($linkdata->media) {
                    $thumb = explode('.', $linkdata->media);
                }
                if ($nm == 0)
                    $firstid = $linkdata->id;
                $nm = 1;
                if (count($templateData) <= 4) {
                  $showplay=0;
                    ?>
                    <div class="imagemultipleBig" >
                        <div class="layers">
                              <?php if (!empty($linkdata->thumb_image)):
                                $showplay =1;
                                ?>
                                <a href="#" onclick="getdetailmenu(<?php echo $linkdata->id ?>,'players1');">
                                <img src="<?php echo base_url() ?>resize.php?img=images/uploads/media/<?php echo $linkdata->thumb_image ?>&w=135&h=175" class="imgss" />
                              <?php else: ?>
                                <?php if (!empty($linkdata->image_from_video)):
                                  $showplay=1;
                                   ?>
                                  <?php echo '<img src="' . base_url() . 'resize.php?img=images/uploads/thumb/' . $linkdata->image_from_video . '&w=135&h=175" class="imgss">'; ?>
                                <?php endif ?>
                              <?php endif ?>
                            </a>
                        </div>
                        <?php if ($showplay): ?>
                        <div id="<?php echo $linkdata->id ?>" class="<?php if ($linkdata->id == $linkId && $view_type == "pdf") echo 'players1'; ?>"><a href="#"></a></div>
                        <?php endif ?>
                    </div>
                    <?php
                }


                if (count($templateData) > 4) {
                    ?>
                    <div class="imagemultiple">
                        <div class="layer"><a href="#" onclick="getdetailmenu(<?php echo $linkdata->id ?>,'players');">
                                <?php if (!empty($linkdata->thumb_image)) { ?>
                                    <img src="<?php echo base_url() ?>resize.php?img=images/uploads/media/<?php echo $linkdata->thumb_image ?>&w=95&h=119" class="imgs"  />
                                    <?php
                                } else {
                                    echo '<img src="' . base_url() . 'resize.php?img=images/uploads/thumb/' . $linkdata->image_from_video . '&w=95&h=119"  class="imgs">';
                                }
                                ?>
                            </a> </div>
                        <div id="<?php echo $linkdata->id ?>"  class="<?php if ($linkdata->id == $linkId && $view_type == "pdf")
                            echo 'players'; ?>"><a href="#"></a></div>
                       <!-- <Div class="caption"><?php // echo $linkdata->link_name;    ?></Div>-->
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <div id="rightpanel" class="col3"> <?php echo $linkData; ?> </div>
</div>
</div>
<!--end of the content area-->
<!--begining of the footer area-->
<?php
// $this->load->view("stage/admin/template_footer.php");
$this->load->view("stage/admin/template_footer" . $display_type . ".php");
if ($totallink <= 4) {
    $size = 'players1';
} else {
    $size = 'players';
}
?>
<p class="pagebreak"></p>


<script type="text/javascript">
<?php if ($view_type != "pdf") { ?>
        $(document).ready(function() {
            getdetailmenu('<?php echo $firstid; ?>','<?php echo $size; ?>');
        });
<?php } ?>
    $sitePath ='<?php echo site_url() ?>';
    var id;
    var overcss;
    function getdetailmenu(detailid,size)
    {
        id=detailid;
        overcss=size;
        $.ajax({
            type:"Post",
            url:$sitePath+"/stage/videolinkdetail",
            data:{"Id":detailid},
            async:false,
            beforeSend:function(){
                //$('#myDiv').length ;
            },
            success:function(response){
                $("#rightpanel").html(response);
                $("#leftpanel .gallery .imagemultipleBig").children().removeClass('players1');
                /*$("#leftpanel .gallery .imagemultiple").children().removeClass('players1');

                        $("#leftpanel .gallery .imagemultipleBig").children().removeClass('players'); */
                $("#leftpanel .gallery .imagemultiple").children().removeClass('players');
                $("#"+id).addClass(overcss)
            }
        })
    }
    /*
    function hoverclass(id)
    {
        $("#"+id).addClass('players1');
    }
     function hoveroutclass(id)
    {
        $("#"+id).removeClass('players1');
    }
     */
</script>
