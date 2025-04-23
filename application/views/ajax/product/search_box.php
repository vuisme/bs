<?php if (isset($data['products']) && count($data['products'])) : ?>
    <table class="table table-bordered table-striped">
        <tbody>
        <?php $stt = 1;
        foreach ((array)$data['products'] as $key => $val) :
            ?>
            <tr style="cursor: pointer;" onclick="cms_detail_product(<?php echo $val['ID']; ?>)">
                <td class="text-center hidden-xs"><?php echo $stt++; ?></td>
                <td class=" " style="font-weight: 600;"><?php echo $val['prd_name']; ?></td>
                <td class="text-center">Mã SP: <?php echo $val['prd_code']; ?></td>
                <td>Giá: <?php echo $val['prd_sell_price']; ?> Tồn: <?php echo $val['prd_sls'] . ' Kg' ?> </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
