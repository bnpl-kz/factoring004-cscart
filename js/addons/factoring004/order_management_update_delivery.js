$(function ($) {
  const form = $('form[name="om_cart_form"]');
  const submitBtn = form.find('[type="submit"]');
  const showLoader = () => $.toggleStatusBox('show', {overlay: form});
  const hideLoader = () => $.toggleStatusBox('hide');
  const showOtpError = (error) => $('#alert-factoring004-otp').prop('hidden', false).text(error);
  const hideOtpError = () => $('#alert-factoring004-otp').prop('hidden', true);
  const id = window.__factoring004.orderId;

  function submitForm () {
    submitBtn.off('click', handleForm);
    submitBtn.click();
  }

  /**
   * @param {Event} e
   */
  function handleForm (e) {
    const status = form.find('input[name="order_status"]').val();
    const paymentId = form.find('[name="payment_id"]').val();

    if (status !== 'C' || paymentId !== window.__factoring004.paymentId) {
      return;
    }

    e.preventDefault();

    $.ajax({
      url: '/admin.php?dispatch=factoring004-delivery',
      method: 'POST',
      dataType: 'json',
      data: {
        order_id: id,
        security_hash: Tygh.security_hash,
      },

      beforeSend: showLoader,
      complete: hideLoader,

      success (data) {
        if (!data.success) {
          $.ceNotification('show', {
            type: 'E',
            title: Tygh.tr('payments.factoring004.shipping_order') + '#' + id,
            message: data.error || Tygh.tr('payments.factoring004.error_occurred'),
          });
          return;
        }

        if (!data.otp) {
          submitForm();
          return;
        }

        $('#factoring004-otp-input-order-id').val(id);
        $('#factoring004-otp').popup({
          autoopen: true,
          autozindex: true,
          escape: false,
          blur: false,
          closebutton: true,
          closebuttonmarkup: `
              <button title="${Tygh.tr('close')}"
                      style="position: absolute;top: 10px;right: 10px;border: none;
                             background: transparent;font-size: 1.5rem;opacity: 0.5;">
                  <span aria-hidden="true">×</span>
              </button>
            `,
          scrolllock: true,

          onopen: () => setTimeout(() => $('#factoring004-otp-input').focus(), 100),
          onclose () {
            $('#factoring004-otp-input').val('');
            hideOtpError();
          },
        });
      },

      error (jqXHR) {
        let response = jqXHR.responseJSON || {error: Tygh.tr('payments.factoring004.error_occurred')};

        $.ceNotification('show', {
          type: 'E',
          title: Tygh.tr('payments.factoring004.shipping_order') + '#' + id,
          message: response.error,
        });
      }
    });
  }

  /**
   * @param {Event} e
   */
  function handleOtpForm (e) {
    e.preventDefault();

    const input = $('#factoring004-otp-input');
    const submitBtn = $('#btn-factoring004-otp');

    $.ajax({
      url: '/admin.php?dispatch=factoring004-delivery-check-otp',
      method: 'POST',
      dataType: 'json',
      data: {
        order_id: $('#factoring004-otp-input-order-id').val(),
        security_hash: Tygh.security_hash,
        otp: input.val(),
      },

      beforeSend () {
        submitBtn.prop('disabled', true);
        submitBtn.text(Tygh.tr('payments.factoring004.checking'));
        hideOtpError();
      },

      complete () {
        submitBtn.prop('disabled', false);
        submitBtn.text(Tygh.tr('payments.factoring004.check'));
      },

      success (data) {
        if (!data.success) {
          showOtpError(data.error);
          return;
        }

        submitForm();
        $('#factoring004-otp').popup('hide');
      },

      error (jqXHR) {
        let response = jqXHR.responseJSON || {error: Tygh.tr('payments.factoring004.error_occurred')};

        showOtpError(response.error);
      }
    });
  }

  /**
   * @returns {HTMLDivElement}
   */
  function createPopup () {
    const popup = document.createElement('div');

    popup.id = 'factoring004-otp';
    popup.hidden = true;
    popup.style.backgroundColor = '#fff';
    popup.style.padding = '1rem';
    popup.style.minWidth = '320px';
    popup.innerHTML = `
    <h3 class="text-center" style="margin-top: 0;">${Tygh.tr('payments.factoring004.check')} OTP</h3>

    <div id="alert-factoring004-otp" class="alert alert-danger text-center" hidden></div>

    <form id="form-factoring004-otp" style="margin-bottom: 0" method="post">
        <div>
            <label for="factoring004-otp-input">OTP</label>
            <input id="factoring004-otp-input"
                   type="text" name="otp" style="width: 100%" minlength="4" maxlength="4" pattern="\\d{4}" required>
        </div>

        <div class="text-center" style="margin: 1rem 0">
            <input id="factoring004-otp-input-order-id" type="hidden" name="order_id">
            <button id="btn-factoring004-otp" class="btn btn-primary">${Tygh.tr('payments.factoring004.check')}</button>
        </div>
    </form>
  `;

    return popup;
  }

  document.body.appendChild(createPopup());
  submitBtn.on('click', handleForm);
  $('#form-factoring004-otp').on('submit', handleOtpForm);
});
