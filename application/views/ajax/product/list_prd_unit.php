<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th class="text-left">Tên đơn vị tính</th>
        <th style="width: 80px;"></th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($_list_prd_unit) && count($_list_prd_unit)) :
        foreach ((array)$_list_prd_unit as $key => $item) :?>
            <tr class='tr-item-<?php echo $item['ID']; ?>'>
                <td class="text-edit"><?php echo $item['ID']; ?></td>
                <td class="text-edit"><?php echo $item['prd_unit_name']; ?></td>
                <td class="text-center"><i class="fa fa-pencil-square-o edit-item" title="Sửa"
                                           onclick="cms_edit_prd('unit',<?php echo $item['ID']; ?>)"
                                           style="margin-right: 10px; cursor: pointer;"></i><i
                            onclick="cms_delete_unit(<?php echo $item['ID'] . ',' . $page; ?>)" title="Xóa"
                            class="fa fa-trash-o delete-item" style="cursor: pointer;"></i></td>
            </tr>
            <tr class='edit-tr-item-<?php echo $item['ID']; ?>' style='display: none;'>
                <td class="text-edit"><?php echo $item['ID']; ?></td>
                <td class="text-edit"><input type="text"
                                             class="form-control edit_prd_unit_name-<?php echo $item['ID']; ?>"
                                             value="<?php echo cms_common_input(isset($item) ? $item : [], 'prd_unit_name'); ?>">
                </td>
                <td class="text-center"><i class='fa fa-floppy-o' title='Lưu'
                                           onclick='cms_update_prdunit(<?php echo $item['ID']; ?>)'
                                           style='color: #EC971F; cursor: pointer; margin-right: 10px;'></i><i
                            onclick='cms_undo_item(<?php echo $item['ID']; ?> )' title='Hủy' class='fa fa-undo'
                            style='color: green; cursor: pointer; margin-right: 5px;'></i></td>
            </tr>
        <?php endforeach;
    else: ?>
        <tr>
            <td colspan="20" class="text-center">Không có dữ liệu</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<?php if (isset($_pagination_link) && !empty($_pagination_link)) { ?>
    <div class="alert alert-info summany-info clearfix" role="alert"
         style="background: #fff; margin-bottom: 0; border: none;">
        <div class="pull-right ajax-pagination">
            <?php echo $_pagination_link; ?>
        </div>
    </div>
<?php } ?>
