<div class="modal fade" tabindex="-1" role="dialog" id="give-payment-modal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Give Payment (Due amount: <span class="text-danger" id="due-amount"></span>)</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="c">
                <form method="POST" id="take-payment-form">
                    <input type="hidden" name="id">

                    <div class="card">
                        <div class="card-body">
                            @livewire('order-payments',[
                                "bankType"=>old('bank_type'),
                                "bankId"=>old('bank_id'),
                                "accountId"=>old('account_id'),
                            ])
                        </div>
                    </div>


                    <div class="form-group" id="amount">
                        <label class="form-inline">Amount</label>
                        <input type="text" name="amount" class="form-control form-control-sm" placeholder="Enter Amount">
                    </div>

                    <div class="form-group" id="date">
                        <label class="form-inline">Payment Date</label>
                        <input type="date" name="date" class="form-control form-control-sm" >
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-sm btn-success mr-3" name="submit">Submit</button>
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Cancle</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>
  </div>
  @push('inner-script')
  <script src="{{asset('js/axios.js')}}"></script>
  <script>

        var modalView =  $('#give-payment-modal');
        modalView.on('hidden.bs.modal', function (e) {
            $(this).find('.error').remove();
            $(this).find(`input`).val('');
            $(this).find(`input`).removeClass('is-invalid');
            $(this).find(`select`).removeClass('is-invalid');
            $(this).find('#due-amount').text('')
        })

        modalView.on('show.bs.modal', function (e) {
            var target = $(e.relatedTarget);
            var id = target.data('id');
            $(this).find(`input[name='id']`).val(id);
            $(this).find('#due-amount').text(target.data('due'))
        })

        $('#take-payment-form').on('submit', function(event){
            event.preventDefault();
            var baseUrl = "{{ route('purchases.index') }}";
            var id = $("input[name='id']").val();
            var url = baseUrl+'/'+id+'/take-payment'
            var bodyFormData = new FormData();
            var amount = $("input[name='amount']").val();
            bodyFormData.append('_token', '{{ csrf_token() }}');
            bodyFormData.append('amount', amount);
            bodyFormData.append('date', $("input[name='date']").val());
            bodyFormData.append('bank_type', $("select[name='bank_type']").val());
            bodyFormData.append('account_id', $("select[name='account_id']").val());
            axios({
                method: "post",
                url: url,
                data: bodyFormData,
            })
            .then(function (response) {
                modalView.modal('hide')
                var dataTable = $('#{{$datatableId}}').dataTable();
                dataTable.api().ajax.reload();

                Swal.fire(
                    'Success!',
                    'Amount: '+amount+' Given Successfully',
                    'success'
                )

            })
            .catch(function (error) {

                $('#give-payment-modal').find('.error').remove();
                $('#give-payment-modal').find(`input`).removeClass('is-invalid');
                $('#give-payment-modal').find(`select`).removeClass('is-invalid');

                $.each(error.response.data, function(index,val){
                    $('#'+index).append(`
                        <span class='text-danger error validation-error invalid-feedback' role="alert">`+val[0]+`</span>
                    `)
                    $('#'+index).find('input').addClass('is-invalid');
                    $('#'+index).find('select').addClass('is-invalid');
                })
            });
        })
  </script>
  @endpush
