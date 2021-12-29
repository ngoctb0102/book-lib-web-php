<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php if($_settings->chk_flashdata('error')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('error') ?>",'error')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Borrows List</h3>
		<!-- <div class="card-tools">
			<a href="?page=order/manage_order" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div> -->
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="30%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Borrow</th>
						<th>Date Return</th>
						<th>Client</th>
						<th>Name</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT b.*,p.title as title from `borrow_his` b inner join `products` p on p.id = b.product_id order by unix_timestamp(b.borrow_date) desc ");
						while($row = $qry->fetch_assoc()):
                            $name = $conn->query("SELECT concat(firstname,' ',lastname) as fname from `clients` where id = '".$row['client_id']."' ");
                            if($fname = $name->fetch_assoc()):
                                $fullname = $fname['fname'];
                            endif;
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['borrow_date'])) ?></td>
                            <?php
                            if(date("Y-m-d H:i",strtotime($row['return_date'])) != '-0001-11-30 00:00'):
                            ?>
                            <td><?php echo date("Y-m-d H:i",strtotime($row['return_date'])) ?></td>
                            <?php else: ?>
                            <td></td>
                            <?php endif; ?>
							<td><?php echo $fullname ?></td>
							<td><?php echo $row['title'] ?></td>
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
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item">Do notthing</a>
									<?php if($row['status'] != 2): ?>
				                    <a class="dropdown-item mask_return" href="javascript:void(0)"  data-id="<?php echo $row['id'] ?>">Mark return</a>
									<?php endif; ?>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function(){
		$('.mask_return').click(function(){
			_conf("Are you sure this book was returned","mask_return",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function mask_return($id){
        // $('.modal').modal('hide')
        // var _this = $('.mask_return[data-id="'+id+'"]')
        // var id = _this.attr('data-id')
        // console.log(id)
        // var item = _this.closest('.card-body')
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master.php?f=mask_return',
            method:'POST',
            data:{id:$id},
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
    // $(function(){
	// 	$('.mask_return').click(function(){
	// 		_conf("Are you sure this book was returned","mask_return",[$(this).attr('data-id')])
	// 	})
	// 	$('.table').dataTable();
	// })
</script>