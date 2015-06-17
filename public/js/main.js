$.fn.existsChecker = function () {
    return this.each(function () {

        $(this).on('focusout', function () {
            var self = $(this),
                selfType = self.attr('id'),
                selfValue,
                feedback = $('.check-exists-feedback[data-type=' + selfType + ']');

            if (selfValue !== self.val()) {
                selfValue = self.val();

                if (selfValue.length > 2) {
                    $.ajax({
                        url: '/check',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            type: selfType,
                            value: selfValue
                        },
                        success: function (data) {
                            if (data.exists !== undefined) {
                                if (data.exists === true) {
                                    feedback.text('Already Taken');
                                    self.parent().closest('div').addClass('has has-error');
                                } else {
                                    feedback.text('That is available');
                                }
                            }
                        },
                        error: function () {
                            //@TODO: Add error for failed AJAX request
                        }

                    });
                }

            }

        });
    })
};
$('form.ajax').on('submit', function () {
    var self = $(this);
    url = self.attr('action'),
        type = self.attr('method'),
        data = {};

    self.find('[name]').each(function (index, value) {
        var self = $(this),
            name = self.attr('name'),
            value = self.val();

        data[name] = value;
    });

    $.ajax({
        url: url,
        type: type,
        data: data,
        success: function (response) {
            console.log(response);
        },
        error: function (error) {
            console.log(error);
        }
    });
    return false;
});