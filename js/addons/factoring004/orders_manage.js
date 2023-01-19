$(function ($) {
  const fn_check_object_status = window.fn_check_object_status;
  const deliveryStatus = 'c';
  const refundStatus = 'e';

  window.fn_check_object_status = function (obj, status, color) {
    if (status.toLowerCase() === deliveryStatus || status.toLowerCase() === refundStatus) {
      return false;
    }

    return fn_check_object_status.apply(this, arguments);
  };

  const container = $('#content_manage_orders');
  const showLoader = () => $.toggleStatusBox('show', {overlay: container});
  const hideLoader = () => $.toggleStatusBox('hide');
  const registerHandler = () => container.on('click', '.cm-ajax.cm-post', handleClick);
  const unRegisterHandler = () => container.off('click', '.cm-ajax.cm-post', handleClick);
  const registerOtpFormHandler = () => $('#form-factoring004-otp').on('submit', handleOtpForm);
  const showOtpError = (error) => $('#alert-factoring004-otp').prop('hidden', false).text(error);
  const hideOtpError = () => $('#alert-factoring004-otp').prop('hidden', true);
  const handleModalAmountForm = () => $('#form-factoring004-refund-amount').on('submit', handleRefund);
  let srcElem = null;

  /**
   * @param {Event} e
   */
  function handleClick (e) {
    const elem = e.target;

    if (elem.classList.contains('status-link-' + deliveryStatus)) {
      e.preventDefault();
      e.stopPropagation();

      const id = new URL(elem.href).searchParams.get('id');

      if (!id) return;

      srcElem = elem;

      process(id, {
        action: 'delivery',
        translateErrorKey: 'shipping_order',
      });

      return;
    }

    if (!elem.classList.contains('status-link-' + refundStatus)) {
      return;
    }

    e.preventDefault();
    e.stopPropagation();

    const id = new URL(elem.href).searchParams.get('id');

    if (!id) return;

    srcElem = elem;

    showAmountPopup(id);
  }

  /**
   * @param {Event} e
   */
  function handleRefund (e) {
    e.preventDefault();

    const id = $('#factoring004-refund-input-order-id').val();
    const amount = $('#factoring004-refund-input-amount').val();

    $('#factoring004-refund-amount-modal').popup('hide');

    process(id, {
      action: 'refund',
      translateErrorKey: 'refund_order',
      data: {
        amount,
      }
    });
  }

  /**
   * @param {string|number} id
   * @param {{action: string, data: Map<string, *>, translateErrorKey: string}} options
   */
  function process (id, options) {
    $.ajax({
      url: '/admin.php?dispatch=factoring004-' + options.action,
      method: 'POST',
      dataType: 'json',
      data: {
        order_id: id,
        security_hash: Tygh.security_hash,
        ...options.data || {},
      },

      beforeSend: showLoader,
      complete: hideLoader,

      success (data) {
        if (!data.success) {
          notifyError(id, options.translateErrorKey, data.error);
          return;
        }

        if (data.otp) {
          const data = options.data || {};

          showOtpPopup(id, options.action, data.amount || 0);
          return;
        }

        unRegisterHandler();
        srcElem.click();
        registerHandler();
      },

      error (jqXHR) {
        let response = jqXHR.responseJSON || {};

        notifyError(id, options.translateErrorKey, response.error);
      }
    });
  }

  /**
   * @param {string|number} id
   */
  function showAmountPopup (id) {
    const input = $('#factoring004-refund-input-amount');

    $('#factoring004-refund-input-order-id').val(id);

    showPopup('#factoring004-refund-amount-modal', {
      onOpen: () => setTimeout(() => input.focus(), 100),
      onClose: () => input.val(''),
    });
  }

  /**
   * @param {string|number} id
   * @param {string} action
   * @param {string|number} amount
   */
  function showOtpPopup (id, action, amount = 0) {
    $('#factoring004-otp-input-order-id').val(id);
    $('#factoring004-otp-input-amount').val(amount);
    $('#factoring004-otp-input-action').val(action);
    showPopup('#factoring004-otp', {
      onOpen: () => setTimeout(() => $('#factoring004-otp-input').focus(), 100),
      onClose () {
        $('#factoring004-otp-input').val('');
        hideOtpError();
        srcElem = null;
      },
    });
  }

  /**
   * @param {string} elemId
   * @param {{onOpen: function, onClose: function}} options
   */
  function showPopup (elemId, options) {
    $(elemId).popup({
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
      onopen: options.onOpen,
      onclose: options.onClose,
    });
  }

  /**
   * @param {string} id
   * @param {string} translateErrorKey
   * @param {string?} message
   */
  function notifyError (id, translateErrorKey, message) {
    $.ceNotification('show', {
      type: 'E',
      title: Tygh.tr('payments.factoring004.' + translateErrorKey) + '#' + id,
      message: message || Tygh.tr('payments.factoring004.error_occurred'),
    });
  }

  /**
   * @param {Event} e
   */
  function handleOtpForm (e) {
    e.preventDefault();

    const input = $('#factoring004-otp-input');
    const submitBtn = $('#btn-factoring004-otp');
    const action = $('#factoring004-otp-input-action').val();
    const amount = $('#factoring004-otp-input-amount').val();

    $.ajax({
      url: '/admin.php?dispatch=factoring004-' + action + '-check-otp',
      method: 'POST',
      dataType: 'json',
      data: {
        order_id: $('#factoring004-otp-input-order-id').val(),
        security_hash: Tygh.security_hash,
        otp: input.val(),
        amount,
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

        unRegisterHandler();
        srcElem.click();
        registerHandler();
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
            <input id="factoring004-otp-input-amount" type="hidden" name="amount">
            <input id="factoring004-otp-input-action" type="hidden">
            <button id="btn-factoring004-otp" class="btn btn-primary">${Tygh.tr('payments.factoring004.check')}</button>
        </div>
    </form>
  `;

    return popup;
  }

  /**
   * @returns {HTMLDivElement}
   */
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
            <label for="factoring004-refund-input-amount">${Tygh.tr('payments.factoring004.amount')}</label>
            <input id="factoring004-refund-input-amount" type="number" name="amount" style="width: 100%" min="0">
            <small>${Tygh.tr('payments.factoring004.amount_helper_text')}</small>
        </div>

        <div class="text-center" style="margin: 1rem 0">
            <input id="factoring004-refund-input-order-id" type="hidden">
            <button class="btn btn-primary">${Tygh.tr('payments.factoring004.refund')}</button>
        </div>
    </form>
  `;

    return popup;
  }

  document.body.appendChild(createPopup());
  document.body.appendChild(createAmountPopup());

  registerHandler();
  registerOtpFormHandler();
  handleModalAmountForm();
});
