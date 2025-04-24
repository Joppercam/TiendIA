<div class="card mt-3">
    <div class="card-header bg-light">
        <h5 class="card-title m-0">Cupón de Descuento</h5>
    </div>
    <div class="card-body">
        <div id="coupon-form">
            @if($cart->coupon_code)
                <div class="alert alert-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Cupón aplicado:</strong> {{ $cart->coupon_code }}
                            <br>
                            <span>Descuento: {{ formatMoney($cart->coupon_discount) }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="remove-coupon">
                            <i class="fa fa-times"></i> Quitar
                        </button>
                    </div>
                </div>
            @else
                <div class="input-group">
                    <input type="text" class="form-control" id="coupon-code" placeholder="Ingrese código de cupón">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" id="apply-coupon">Aplicar</button>
                    </div>
                </div>
                <div id="coupon-message" class="mt-2"></div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const applyCouponBtn = document.getElementById('apply-coupon');
        const removeCouponBtn = document.getElementById('remove-coupon');
        const couponCodeInput = document.getElementById('coupon-code');
        const couponMessage = document.getElementById('coupon-message');
        
        if (applyCouponBtn) {
            applyCouponBtn.addEventListener('click', function() {
                const code = couponCodeInput.value.trim();
                
                if (!code) {
                    couponMessage.innerHTML = '<div class="alert alert-warning">Por favor ingrese un código de cupón.</div>';
                    return;
                }
                
                fetch('{{ route("shop.coupon.apply") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: code })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        couponMessage.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    couponMessage.innerHTML = '<div class="alert alert-danger">Ha ocurrido un error al aplicar el cupón.</div>';
                });
            });
        }
        
        if (removeCouponBtn) {
            removeCouponBtn.addEventListener('click', function() {
                fetch('{{ route("shop.coupon.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
    });
</script>
@endpush