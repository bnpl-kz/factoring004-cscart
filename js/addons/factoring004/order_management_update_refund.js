$(function ($) {
  const form = $('form[name="om_cart_form"]');
  const submitBtn = form.find('[type="submit"]');
  const showLoader = () => $.toggleStatusBox('show', {overlay: form});
  const hideLoader = () => $.toggleStatusBox('hide');
  const showOtpError = (error) => $('#alert-factoring004-refund-otp').prop('hidden', false).text(error);
  const hideOtpError = () => $('#alert-factoring004-refund-otp').prop('hidden', true);
  const id = window.__factoring004.orderId;

  function submitForm () {
    submitBtn.off('click', handleClick);
    submitBtn.click();
  }

  /**
   * @param {string|number} id
   */
  function showAmountPopup (id) {
    const input = $('#factoring004-refund-input-amount');

    $('#factoring004-refund-input-order-id').val(id);
    $('#factoring004-refund-amount-modal').popup({
      autoopen: true,
      autozindex: true,
      escape: false,
      blur: false,
      closebutton: true,
      closebuttonmarkup: `
        <button title="Close"
                style="position: absolute;top: 10px;right: 10px;border: none;
                       background: transparent;font-size: 1.5rem;opacity: 0.5;">
            <span aria-hidden="true">×</span>
        </button>
      `,
      scrolllock: true,

      onopen: () => setTimeout(() => input.focus(), 100),
      onclose: () => input.val(''),
    });
  }

  /**
   * @param {string|number} id
   * @param {string|number} amount
   */
  function showOtpPopup (id, amount) {
    $('#factoring004-refund-otp-input-order-id').val(id);
    $('#factoring004-refund-otp-input-amount').val(amount);
    $('#factoring004-refund-otp-modal').popup({
      autoopen: true,
      autozindex: true,
      escape: false,
      blur: false,
      closebutton: true,
      closebuttonmarkup: `
        <button title="Close"
                style="position: absolute;top: 10px;right: 10px;border: none;
                       background: transparent;font-size: 1.5rem;opacity: 0.5;">
            <span aria-hidden="true">×</span>
        </button>
      `,
      scrolllock: true,

      onopen: () => setTimeout(() => $('#factoring004-refund-otp-input').focus(), 100),
      onclose () {
        $('#factoring004-refund-otp-input').val('');
        $('#factoring004-refund-otp-input-amount').val('');
        hideOtpError();
      },
    });
  }

  /**
   * @param {string|number} id
   * @param {string} error
   */
  function notifyError (id, error) {
    $.ceNotification('show', {
      type: 'E',
      title: 'Refund order #' + id,
      message: error,
    });
  }

  function handleClick (e) {
    const status = form.find('input[name="order_status"]').val();
    const paymentId = form.find('[name="payment_id"]').val();

    if (status !== 'E' || paymentId !== window.__factoring004.paymentId) {
      return;
    }

    e.preventDefault();
    showAmountPopup(id);
  }

  /**
   * @param {Event} e
   */
  function handleForm (e) {
    e.preventDefault();

    const amount = $('#factoring004-refund-input-amount').val();

    $('#factoring004-refund-amount-modal').popup('hide');

    $.ajax({
      url: '/admin.php?dispatch=factoring004-refund',
      method: 'POST',
      dataType: 'json',
      data: {
        order_id: id,
        amount,
        security_hash: Tygh.security_hash,
      },

      beforeSend: showLoader,
      complete: hideLoader,

      success (data) {
        if (!data.success) {
          notifyError(id, data.error || 'An error occurred');
          return;
        }

        if (data.otp) {
          showOtpPopup(id, amount);
          return;
        }

        submitForm();
      },

      error (jqXHR) {
        let response = jqXHR.responseJSON || {error: 'An error occurred'};

        notifyError(id, response.error);
      }
    });
  }

  /**
   * @param {Event} e
   */
  function handleOtpForm (e) {
    e.preventDefault();

    const input = $('#factoring004-refund-otp-input');
    const submitBtn = $('#btn-factoring004-refund-otp');

    $.ajax({
      url: '/admin.php?dispatch=factoring004-refund-check-otp',
      method: 'POST',
      dataType: 'json',
      data: {
        order_id: $('#factoring004-refund-otp-input-order-id').val(),
        security_hash: Tygh.security_hash,
        otp: input.val(),
        amount: $('#factoring004-refund-otp-input-amount').val(),
      },

      beforeSend () {
        submitBtn.prop('disabled', true);
        submitBtn.text('Checking');
        hideOtpError();
      },

      complete () {
        submitBtn.prop('disabled', false);
        submitBtn.text('Check');
      },

      success (data) {
        if (!data.success) {
          showOtpError(data.error);
          return;
        }

        submitForm();
        $('#factoring004-refund-otp-modal').popup('hide');
      },

      error (jqXHR) {
        let response = jqXHR.responseJSON || {error: 'An error occurred'};

        showOtpError(response.error);
      }
    });
  }

  /**
   * @returns {HTMLDivElement}
   */
  function createPopup () {
    const popup = document.createElement('div');

    popup.id = 'factoring004-refund-otp-modal';
    popup.hidden = true;
    popup.style.backgroundColor = '#fff';
    popup.style.padding = '1rem';
    popup.style.minWidth = '320px';
    popup.innerHTML = `
    <h3 class="text-center" style="margin-top: 0;">Check OTP</h3>

    <div id="alert-factoring004-refund-otp" class="alert alert-danger text-center" hidden></div>

    <form id="form-factoring004-refund-otp" style="margin-bottom: 0" method="post">
        <div>
            <label for="factoring004-refund-otp-input">OTP</label>
            <input id="factoring004-refund-otp-input"
                   type="text" name="otp" style="width: 100%" minlength="4" maxlength="4" pattern="\\d{4}" required>
        </div>

        <div class="text-center" style="margin: 1rem 0">
            <input id="factoring004-refund-otp-input-order-id" type="hidden" name="order_id">
            <input id="factoring004-refund-otp-input-amount" type="hidden" name="amount">
            <button id="btn-factoring004-refund-otp" class="btn btn-primary">Check</button>
        </div>
    </form>
  `;

    return popup;
  }

  function createAmountPopup () {
    const popup = document.createElement('div');

    popup.id = 'factoring004-refund-amount-modal';
    popup.hidden = true;
    popup.style.backgroundColor = '#fff';
    popup.style.padding = '1rem';
    popup.style.minWidth = '320px';
    popup.innerHTML = `
    <form id="form-factoring004-refund-amount" style="margin-bottom: 0">
        <div>
            <label for="factoring004-refund-input-amount">Amount</label>
            <input id="factoring004-refund-input-amount" type="number" name="amount" style="width: 100%" min="0">
            <small>Leave this field is empty or enter 0 to full refund, otherwise partial refund will be made</small>
        </div>

        <div class="text-center" style="margin: 1rem 0">
            <input id="factoring004-refund-input-order-id" type="hidden">
            <button class="btn btn-primary">Refund</button>
        </div>
    </form>
  `;

    return popup;
  }

  document.body.appendChild(createPopup());
  document.body.appendChild(createAmountPopup());

  submitBtn.on('click', handleClick);
  $('#form-factoring004-refund-otp').on('submit', handleOtpForm);
  $('#form-factoring004-refund-amount').on('submit', handleForm);
});