<script type="text/javascript" src="<?php echo base_url() ?>assets/shared/js/common.js"></script>
<!-- TEMPLATE 0 -->
<!--begining of the content area-->
<div id ="contentArea" class="imgMarginLeft pad10 row clear tmpl<?=$textPosition?> previewTemplate1">

				<?php
					$data['commonTemplateData'] = $templateData;
				 	echo $this->load->view('stage/parts/_section_text_position',$data,false);
				 ?>

        <div <?php if ($templateData)
            echo 'id="leftpanels" class="'.$lftCls.'"'; ?>>
            <?php
            if ($downloadData) {
                echo
                '<ul class="downloads">';
                foreach ($downloadData as $rows) {
                    if ($rows->link_file) {
                        echo '<li><a href="' . base_url() . 'open_file.php?file_name=' . $rows->link_file . '">&nbsp;&nbsp;';
                        if ($rows->link_name)
                            echo $rows->link_name;
                        else
                            echo 'View File';
                        echo '</a></li>';
                    }
                }
                echo '</ul>';
            }
            if ($firstStep == "1" && $detailStart == "1")
                echo '<div style="clear:both; margin-top:15px;">' . $firstTemplateData . '</div>';
            ?>
        </div>

</div>
    <!--end of the content area-->
</div>
