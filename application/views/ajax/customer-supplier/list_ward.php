<option value="-1">Chọn Phường / Xã</option>
<?php
foreach ((array)$list_ward as $item) {
    ?>
    <option value="<?php echo $item['ID'] ?>"><?php echo $item['ward_name'] ?></option>
    <?php
}
?>
