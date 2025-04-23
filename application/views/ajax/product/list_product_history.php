<table class="table table-hover table-custom">
    <thead>
    <th>Thời gian</th>
    <th class="hidden-xs">Nhân viên</th>
    <th>Thao tác</th>
    <th>Mã phiếu</th>
    <th class="text-center">SL</th>
    <th class="text-center <?php if (CMS_SERIAL == 0 || $data['prd_serial'] == 0) echo ' hidden' ?>">Serial</th>
    <th class="hidden-xs">Tại chi nhánh</th>
    </thead>
    <tbody>
    <?php if (isset($data['_list_history']) && count($data['_list_history'])) :
        foreach ((array)$data['_list_history'] as $key => $item) : ?>
            <tr>
                <td class="text-left"><span
                            class="hidden visible-xs"><?php echo cms_ConvertDate($item['created']); ?></span><span
                            class="hidden-xs"><?php echo cms_ConvertDateTime($item['created']); ?></span></td>
                <td class="hidden-xs"><?php echo $item['display_name']; ?></td>
                <td><?php echo cms_getNameReportTypeByID($item['type']); ?></td>
                <td><?php echo $item['transaction_code']; ?></td>
                <td class="text-center"><?php echo $item['quantity'] == 0 ? 0 : ($item['output'] > 0 ? '-' . ($item['input'] + $item['output']) : '+' . ($item['input'] + $item['output'])); ?></td>
                <td class="text-center <?php if (CMS_SERIAL == 0 || $data['prd_serial'] == 0) echo ' hidden' ?>"><?php echo cms_convertserial($item['report_serial']); ?></td>
                <td class="hidden-xs"><?php echo $item['store_name']; ?></td>
            </tr>
        <?php endforeach;
    else :
        echo '<tr><td colspan="90" class="text-center">Không có dữ liệu</td></tr>';
    endif;
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="100"><?php echo $display ?>
        </td>
    </tr>
    </tfoot>
</table>
<div class="pull-right ajax-pagination">
    <?php echo $_pagination_link; ?>
</div>
