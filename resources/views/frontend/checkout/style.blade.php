<style>
 .payment-section {
    margin-top: 25px;
}

.payment-heading {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 16px;
}

.checkout-box {
    background: #f3f9fb;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 22px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
}

.checkout-title {
    font-size: 18px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 18px;
}

.checkout-title span {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
    margin-top: 3px;
}

.method-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 14px;
    width: 100%;
    padding: 14px 15px;
    margin-bottom: 12px;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.25s ease;
}

.method-option:hover {
    border-color: #2563eb;
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.08);
    transform: translateY(-1px);
}

.method-option input {
    display: none;
}

.radio-ui {
    width: 20px;
    height: 20px;
    border: 2px solid #cbd5e1;
    border-radius: 50%;
    flex: 0 0 20px;
    position: relative;
}

.method-option input:checked + .radio-ui {
    border-color: #2563eb;
}

.method-option input:checked + .radio-ui::after {
    content: "";
    width: 10px;
    height: 10px;
    background: #2563eb;
    border-radius: 50%;
    position: absolute;
    inset: 3px;
}

.method-option:has(input:checked) {
    border-color: #2563eb;
    background: #eff6ff;
}

.method-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: 18px;
    flex: 0 0 42px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.12);
}

.method-icon.bkash {
    background: #e2136e;
}

.method-icon.nagad {
    background: #f5821f;
}

.method-icon.rocket {
    background: #8b2fc9;
}

.method-icon.cod {
    background: #2e7d32;
}

.method-icon.card {
    background: #1a1f71;
    font-size: 10px;
}

.method-info {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 0;
}

.method-info strong {
    font-size: 15px;
    color: #111827;
    margin-bottom: 2px;
}

.method-info small {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;
}

.popular-badge {
    background: #e2136e;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 9px;
    border-radius: 999px;
    white-space: nowrap;
}

.pay-btn {
    width: 100%;
    border: none;
    margin-top: 12px;
    padding: 15px 18px;
    border-radius: 16px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    font-size: 16px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 14px 28px rgba(37, 99, 235, 0.25);
    transition: all 0.25s ease;
}

.pay-btn span {
    display: block;
    font-size: 12px;
    font-weight: 500;
    opacity: 0.9;
    margin-top: 2px;
}

.pay-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 36px rgba(37, 99, 235, 0.32);
}

.trust-strip {
    margin-top: 14px;
    padding: 10px 12px;
    background: #f1f5f9;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    color: #475569;
}

.trust-strip small {
    font-size: 12px;
}

/* Responsive */
@media (max-width: 575px) {
    .checkout-box {
        padding: 16px;
        border-radius: 16px;
    }

    .checkout-title {
        font-size: 16px;
    }

    .method-option {
        gap: 10px;
        padding: 12px;
        border-radius: 14px;
    }

    .method-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        flex-basis: 38px;
    }

    .method-info strong {
        font-size: 14px;
    }

    .method-info small {
        font-size: 11px;
    }

    .popular-badge {
        display: none;
    }

    .pay-btn {
        padding: 13px 15px;
        font-size: 15px;
    }
}   

/*-------*/
.order-summary-card {
    background: #ffffff;
    padding: 22px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
}
.card-body{
background: #ffffff;
}
.order-summary-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 18px;
}

.order-summary-header h4 {
    margin: 0;
    font-size: 22px;
    font-weight: 800;
    color: #111827;
}

.order-summary-header p {
    margin: 4px 0 0;
    font-size: 13px;
    color: #6b7280;
}

.item-count {
    background: #eff6ff;
    color: #2563eb;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 12px;
    border-radius: 999px;
    white-space: nowrap;
}

.order-table-wrap {
    border: 1px solid #eef2f7;
    border-radius: 16px;
    overflow: hidden;
}

.order-table {
    margin-bottom: 0;
}

.order-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 13px;
    font-weight: 800;
    border-bottom: 1px solid #e5e7eb;
    padding: 13px 14px;
}

.order-table tbody td {
    padding: 14px;
    font-size: 14px;
    color: #111827;
    border-color: #f1f5f9;
}

.product-name {
    font-weight: 700;
    color: #111827;
    line-height: 1.4;
}

.qty-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 28px;
    padding: 0 10px;
    background: #f1f5f9;
    color: #0f172a;
    border-radius: 999px;
    font-weight: 800;
    font-size: 13px;
}

.summary-total-box {
    margin-top: 18px;
    background: #f3f9fb;
    
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 16px;
}

.summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 9px 0;
    color: #475569;
    font-size: 14px;
}

.summary-row strong {
    color: #111827;
    font-size: 15px;
}

/* .summary-row.discount span,
.summary-row.discount strong {
    color: #dc2626;
} */

.summary-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 10px 0;
}

.grand-total {
    padding-top: 12px;
}

.grand-total span {
    font-size: 17px;
    font-weight: 800;
    color: #111827;
}

.grand-total strong {
    font-size: 24px;
    color: #16a34a;
    font-weight: 900;
}

/* Responsive */
@media (max-width: 575px) {
    .order-summary-card {
        padding: 16px;
        border-radius: 18px;
    }

    .order-summary-header {
        align-items: flex-start;
    }

    .order-summary-header h4 {
        font-size: 19px;
    }

    .order-table-wrap {
        overflow-x: auto;
    }

    .order-table {
        min-width: 540px;
    }

    .grand-total strong {
        font-size: 20px;
    }
}

/*----------*/


.delivery-box{
    margin-top:15px;
    margin-bottom:10px;
}

.delivery-title{
    font-weight:700;
    margin-bottom:12px;
}

.delivery-option{
    display:flex;
    align-items:center;
    gap:12px;
    border:1px solid #e5e7eb;
    padding:14px;
    border-radius:14px;
    margin-bottom:10px;
    cursor:pointer;
    transition:.3s;
}

.delivery-option:hover{
    border-color:#0d6efd;
    background:#f8fbff;
}

.delivery-option input{
    display:none;
}

.radio-ui{
    width:20px;
    height:20px;
    border:2px solid #cbd5e1;
    border-radius:50%;
    position:relative;
}

.delivery-option input:checked + .radio-ui{
    border-color:#0d6efd;
}

.delivery-option input:checked + .radio-ui:after{
    content:'';
    width:10px;
    height:10px;
    background:#0d6efd;
    border-radius:50%;
    position:absolute;
    top:3px;
    left:3px;
}

.delivery-price{
    margin-left:auto;
    font-weight:700;
    color:#198754;
}

@media(max-width:576px){

.delivery-option{
    padding:10px;
}

.delivery-price{
    font-size:13px;
}

}
</style>