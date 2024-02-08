@extends('home')

@section('section')

<script>
    var dtTable;
    var viewDialog;
    var viewDialogHTML;
	var selectedRow =[];
    $(document).ready(function(){
		$.fn.dataTable.ext.errMode = 'none';
		dtTable = $('#dttable').dataTable({
			"aoColumns" : [
				{"data": "encid","bSortable" : false},
				{"data": "name","bSortable" : false},
				{"data": "emp_no","bSortable" : true},
				{"data": "email","bSortable" : false},
				{"data": "mobile","bSortable" : false},
				{"data": "leave_type","bSortable" : false},
				{"data": "supervisor","bSortable" : false},
				{"data": "approve_status","bSortable" : false},
				{"data": "lmd","bSortable" : true},
				{"data": "action", "bSortable" : false, "render": function (data, type, row) {
					return data; // This will render the custom HTML provided in the 'action' field
				}},
			],
			"aaSorting": [[8, 'desc']],
			"processing": true,
			"fnDrawCallback" : function() {
				$('.js-init-tooltip').tooltip();
				$('.js-row-select').change(function() {
					var rowID = $(this).val();
					if(this.checked && !selectedRow.includes(rowID)) {
						selectedRow.push(rowID);
					} else {
						selectedRow = selectedRow.filter(function(row){
							return row !== rowID;
						});
					}
					enableDisableButton();	
				});	
			},
			"pageLength": "{{DEFAULT_TABLE_ROW}}",
			// "lengthMenu": [[10,25, 50, 100, -1], [10,25, 50, 100, 'All']],
			"responsive" : true,
			"bAutoWidth": false,
			"searching": true,
			"bServerSide" : true,
			"ajax" : {
				"url" : "{{ route('leave.approval.list') }}",
				"data" : function(d) {
					d.status = window.localStorage.status ? window.localStorage.status : $('#status').val();
					d.type = window.localStorage.type ? window.localStorage.type : $('#type').val();
				},			
			}
		});
        viewDialogHTML = $(".js-view-dialog").html();
		$(".js-view-dialog").html('');

		$(".js-row-select-all").change(function() {
			selectedRow.length = 0;
			if (this.checked) {
				$('.js-row-select').prop('checked', true).trigger('change');
			} else {
				$('.js-row-select').prop('checked', false).trigger('change');
			}
		});

		var searchInput = $('#dttable_filter').find('input[type="search"]').detach();
		var searchIcon = '<i class="bi bi-search search-icon"></i>'
    	$('.search-bar').append(searchInput);
		searchInput.addClass('search-box form-control');
		searchInput.attr('placeholder','Search');
    	$('.search-box').after(searchIcon);
		
		$('#dttable_filter').detach();
		var length = $('#dttable_length').detach();
		length.addClass('float-left');
		$('.page-length').append(length);

		$("#status").change(function() {
			window.localStorage.status = ''; 
			refreshDataTable(true);
		});
		$("#type").change(function() {
			window.localStorage.type = ''; 
			refreshDataTable(true);
		});
    });

	function refreshDataTable() {
		dtTable.fnDraw(true);
		selectedRow.length = 0;
		$('.js-row-select').prop('checked', false);
		$('.js-row-select-all').prop('checked', false);
		enableDisableButton();
	}

	function enableDisableButton() {
		var btns = $('.bulk-btn-func');
		if(selectedRow.length > 0) {
			btns.attr('disabled',false)
		} else {
			btns.attr('disabled',true)
		}
	}

	function closeViewRecord() {
		viewDialog.modal('hide');
	}

    function viewRecord(id) {
		viewDialog = bootbox.dialog({
			message: viewDialogHTML,
			title: "View Leave",
			className:"modal-lg"
		})
		.on("shown.bs.modal", function(e) {
			$.ajax({
				url:'{{route("leave.show")}}',
				method : 'POST',
				data : {
					id: id,
					_token: '{{ csrf_token() }}' 
				},
				success : function(response) {
					var leave = response.leave;

					$('.js-leave-name').text(leave.staff_name); 
					$('.js-leave-email').text(leave.staff_email); 
					$('.js-leave-emp').text(leave.staff_emp); 
					$('.js-leave-mobile').text(leave.staff_mobile); 								
					$('.js-leave-type').text(leave.leave_rtype); 
					$('.js-leave-from').text(leave.leave_from);
					$('.js-leave-rdate').text(leave.report_date);
					$('.js-leave-days').text(leave.days);
					$('.js-leave-super').text(leave.supervisor); 
					$('.js-leave-to').text(leave.leave_to);
					$('.js-leave-resper').text(leave.res_person);
					$('.js-leave-reas').text(leave.reason);			
				},
				error : function(error) {
					closeViewRecord();
					showMessage('error',error.responseJSON.message);
				} 
			});
		});
	}

    function exportToExcel() {
		$.ajax({
			url: '{{ route("leave.export") }}',
			method: 'POST',
			data: {
				id : selectedRow,
				_token: '{{ csrf_token() }}' 
			},
			xhrFields: {
				responseType: 'blob' 
			},
			success: function(response) {
				var url = window.URL.createObjectURL(response);
				var a = document.createElement('a');
				a.href = url;
				var now = new Date().toLocaleString();
				a.download = 'leave_'+now+'.xlsx'; 
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
				$(a).remove();
			},
			error: function(error) {
				showMessage('error', error);
			} 
		});	
	}

	function approveRecord(id) {
		confirmCustomPopup("Approve Leave", "Are you sure to approve this leave?", 'Y/N', function() {
			sendAjaxRequest('{{route("leave.approval.status")}}', {
				'id': id,
				'act': 'approve',
				'_token': '{{ csrf_token() }}'
			});
		});
	}
	
	function unapproveRecord(id) {
		confirmCustomPopup("Unapprove Leave", "Are you sure to not approve this leave?", 'Y/N', function() {
			sendAjaxRequest('{{route("leave.approval.status")}}', {
				'id': id,
				'act': 'notapprove',
				'_token': '{{ csrf_token() }}'
			});
		});
	}

</script>

<div class="page-body ps-2">
	<div class="row justify-content-between py-2">
		<div class="col-auto">
			<p class="m-0 page-title">Leave Approval</p>
		</div>
		<div class="col-auto">
			<ol class="p-0 m-0 breadcrumb">
				<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item active" aria-current="page">My leaves</li>
			</ol>
		</div>
	</div>
	<div class="row justify-content-between py-2 mb-2 page-btn-line">
		<div class="col-auto">
		</div>
    <div class="col-8">
        <div class="search-bar d-flex"></div>
    </div>
    <div class="col-auto">
        <div class="js-bulk-btn">          
            @if($exportGranted)
                <button onclick="exportToExcel();" type="button" class="ml-2 btn btn-sm bulk-btn-func" disabled="true" data-inline="true" title="Delete">
                    <i class="bi bi-filetype-xlsx xlsx-btn"></i>
                </button>
            @endif
        </div>
    </div>
	<div class="col-12 d-flex mb-2 mt-3">
		<div class="col-2 page-length"></div>
		<div class="col-9 d-flex">
			<div class="me-2">
				<label for="type">Leave Type</label>
				<select name="type" id="type">
					<option value="">All</option>
					@foreach($leaves as $key => $value)
						<option value="{{ $key }}">{{ $value }}</option>
					@endforeach
				</select>
			</div>
			<div class="me-2">
				<label for="status">Status</label>
				<select name="status" id="status">
					<option value="">All</option>
					@foreach($status as $key => $value)
						<option value="{{ $key }}">{{ $value }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-1">
			<a class="float-end" href="javascript:refreshDataTable()"><i class="bi bi-arrow-clockwise"></i></a>
		</div>
	</div>
	
	<table id="dttable"	class="table table-striped table-hover" style="width:100%" >
	<thead>
		<tr>
		<th><input type="checkbox" class="js-row-select-all"></th>
		<th>Name</th>
		<th>Emp. No</th>
		<th>Email</th>
		<th>Mobile</th>
		<th>Leave Type</th>
		<th>Supervisor</th>
		<th>Apr. Status</th>
		<th>Last Modified Date</th>
		<th>Action</th>
		</tr>
	</thead>
	<tbody>
					
	</tbody>
	</table>
</div>
<div class="d-none">

    <div class="js-view-dialog">
		<div class="row form-horizontal">
			<div class="col-md-12">
				<div class="row justify-content-between">
					<div class="col-md-6">
						<div class="form-group">
							<label class="mb-1 control-label">Name</label>
							<div class="js-leave-name"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">Emp No</label>
							<div class="js-leave-emp"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">Leave Type</label>
							<div class="js-leave-type"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">From</label>
							<div class="js-leave-from"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">Report Date</label>
							<div class="js-leave-rdate"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">Days</label>
							<div class="js-leave-days"></div>
						</div>			
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="mb-1 control-label">Email</label>
							<div class="js-leave-email"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">Mobile</label>
							<div class="js-leave-mobile"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">Direct Supervisor</label>
							<div class="js-leave-super"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">To</label>
							<div class="js-leave-to"></div>
						</div>
						<div class="form-group">
							<label class="mb-1 control-label">Responsible Person</label>
							<div class="js-leave-resper"></div>
						</div>						
						<div class="form-group">
							<label class="mb-1 control-label">Reason</label>
							<div class="js-leave-reas"></div>
						</div>	
					</div>						
					<div class="row justify-content-end">
						<div class="col-auto">
							<div class="form-group foot mb-0">
								<button type="button" class="btn btn-sm mr-1" onclick="javascript:closeViewRecord()">Close</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>   
@endsection