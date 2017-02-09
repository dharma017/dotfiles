<?php
/*
* Template : Ladder follow up Template [Admin Preview section]
* Created on :
* Last Updated :
* Template Type : Form
* Created By :  Web Search Professional.
*/
$this->load->view("stage/admin/template_header.php")
?>
<!--end  of the head area-->
<form action="#">
    <!--begining of the content area-->
    <div id ="contentArea" class="  pad10 row clear previewTemplate12">
        <?php
        if ($description)
            echo '<div class="clear wrapper600">' . $description . '</div>';
        ?>
        <div class="formentry">
            <div class="childrating">
                <div id="ladder" class="col1"> </div>
                <div id="rating" class="col2">
                    <p>
                        <label class="number dim">100</label>
                        <textarea class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">90</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">80</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">70</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">60</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">50</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">40</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">30</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">20</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                    <p>
                        <label class="number dim">10</label>
                        <textarea  class="inputs"></textarea>
                        <input type="text" class="small_text inputs" />
                    </p>
                </div>
            </div>
            <p> </p>
        </div>
    </div>
    <!--end of the content area-->
</form>
    </div>
<!--begining of the footer area-->
<?php $this->load->view("stage/admin/template_footer.php") ?>