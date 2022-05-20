$(function ($) {
  const disallowedValues = ['C', 'P', 'E'];

  $('#orders_list_form').on('click', 'a[data-ca-target-form="#orders_list_form"]', function (e) {
    const params = new URL(e.target.href).searchParams;

    if (disallowedValues.indexOf(params.get('status')) === -1) {
      return;
    }

    e.preventDefault();
    e.stopPropagation();

    $.ceNotification('show', {
      type: 'W',
      title: Tygh.tr('payments.factoring004.bulk_orders_editing'),
      message: Tygh.tr('payments.factoring004.action_is_not_allowed'),
    });
  });
});
