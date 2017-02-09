
<?php
/*
 * Template : Ladder Follow Up Template [Admin Form Section]
 * Created on : 
 * Last Updated :
 * Template Type : Form
 * Created By :  Web Search Professional.
 */
?>
<div class="box box-100"> 
    <!-- box full-width -->
    <div class="boxin">
        <div class="header">
            <h3><?php echo $templateHeading; ?> </h3>
        </div>
        <script language="javascript">
            $("#ladder li img").click(function(){		
                pos = $(this).attr("id");
                $("#rad_"+pos).removeAttr("checked"); 
                $("#rad_"+pos).attr("checked", "checked");

            });
        </script>

        <form name="frmAddStep" id="frmAddStep" method="post">
            <?php
            $this->load->view("stage/admin/template_common_form");
            $this->load->view("stage/admin/template_form_footer");
            ?>   
        </form>
    </div>
</div>
