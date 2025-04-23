<table class="table table-hover table-custom">
    <thead>
    <th>Mã SP</th>
    <th class="text-left">Tên SP</th>
    <th class="text-center">Định mức tối thiểu</th>
    <th class="text-center">Định mức tối đa</th>
    <th class="text-center">Tồn</th>
    <th>Kho</th>
    </thead>
    <tbody>
    <?php if (isset($prd) && count($prd)) :
        foreach ((array)$prd as $key => $item) : ?>
            <tr>
                <td><?php echo $item['prd_code']; ?></td>
                <td><?php echo $item['prd_name']; ?></td>
                <td class="text-center"><?php echo $item['prd_min']; ?></td>
                <td class="text-center"><?php echo $item['prd_max']; ?></td>
                <td class="text-center"><?php echo $item['quantity']; ?></td>
                <td><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
            </tr>
        <?php endforeach;
    else :
        echo '<tr><td colspan="20" class="text-center">Không có dữ liệu</td></tr>';
    endif;
    ?>
    </tbody>
</table>
