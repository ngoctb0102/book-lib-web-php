    
<section class="py-2">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body">
                <div class="w-100 justify-content-between d-flex">
                    <h4><b>Borrow history</b></h4>
                    <a href="./?p=edit_account" class="btn btn btn-dark btn-flat"><div class="fa fa-user-cog"></div> Manage Account</a>
                    <a href="./?p=my_account" class="btn btn btn-dark btn-flat"><div class="fa fa-user-cog"></div> Orders</a>
                </div>
                    <hr class="border-warning">
                    <table class="table table-stripped text-dark">
                        <colgroup>
                            <col width="10%">
                            <col width="10">
                            <col width="15">
                            <col width="35">
                            <col width="10">
                            <col width="10">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Borrow Time</th>
                                <th>Return Time</th>
                                <th>Book Name</th>
                                <th>Borrow Status</th>
                                <th>Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $i = 1;
                                $qry = $conn->query("SELECT b.* from `borrow_his` b where b.client_id = '".$_settings->userdata('id')."' order by unix_timestamp(b.borrow_date) desc ");
                                while($row = $qry->fetch_assoc()):
                                    $book_q = $conn->query("SELECT title from `products` where id = '".$row['product_id']."' ");
                                    if($book_title = $book_q->fetch_assoc()):
                                        $book_t = $book_title['title'];
                                    endif;
                            ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo date("Y-m-d H:i",strtotime($row['borrow_date'])) ?></td>
                                    <?php
                                    if(date("Y-m-d H:i",strtotime($row['return_date'])) != '-0001-11-30 00:00'):
                                    ?>
                                    <td><?php echo date("Y-m-d H:i",strtotime($row['return_date'])) ?></td>
                                    <?php else: ?>
                                    <td></td>
                                    <?php endif; ?>
                                    <td><?php echo $book_t ?> </td>
                                    <td class="text">
                                            <?php if($row['status'] == 1): ?>
                                                <span class="badge badge-light text-dark">Borrowing</span>
                                            <?php elseif($row['status'] == 2): ?>
                                                <span class="badge badge-primary">Returned</span>
                                            <?php elseif($row['status'] == 3): ?>
                                                <span class="badge badge-warning">Returnning</span>
                                            <?php else: ?>
                                                <!-- <span class="badge badge-danger">Cancelled</span> -->
                                            <?php endif; ?> 
                                        </td>
                                    <td>
                                        <?php if($row['status'] == 1): ?>
                                            <span class="mr-2"><a href="javascript:void(0)" class="btn btn-sm btn-outline-danger return_item" data-id="<?php echo $row['id'] ?>"><i class="fas fa-book-dead"></i></a></span>
                                        <?php endif; ?>
                                    </td>
                                    </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</section>
<script>
    function cancel_book($id){
        start_loader()
        $.ajax({
            url:_base_url_+"classes/Master.php?f=update_book_status",
            method:"POST",
            data:{id:$id,status:2},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("an error occured",'error')
                end_loader()
            },
            success:function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    alert_toast("Book cancelled successfully",'success')
                    setTimeout(function(){
                        location.reload()
                    },2000)
                }else{
                    console.log(resp)
                    alert_toast("an error occured",'error')
                }
                end_loader()
            }
        })
    }
    function return_item(id){
        // $('.modal').modal('hide')
        var _this = $('.return_item[data-id="'+id+'"]')
        var id = _this.attr('data-id')
        console.log(id)
        // var item = _this.closest('.card-body')
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master.php?f=return_item',
            method:'POST',
            data:{id:id},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("an error occured", 'error');
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    location.reload()
                    end_loader()
                }else{
                    alert_toast("an error occured", 'error');
                    end_loader()
                }
            }

        })
    }
    $(function(){
        $('.view_order').click(function(){
            uni_modal("Order Details","./admin/orders/view_order.php?view=user&id="+$(this).attr('data-id'),'large')
        })
        $('.return_item').click(function(){
            _conf("Please give book to shipper when they come !",'return_item',[$(this).attr('data-id')])
        })
        $('table').dataTable();

    })
</script>