<option value="-1">Chọn Quận/Huyện</option>
<?php
foreach ((array)$list_district as $item) {
    ?>
    <option value="<?php echo $item['ID'] ?>"><?php echo $item['district_name'] ?></option>
    <?php
}
?>
