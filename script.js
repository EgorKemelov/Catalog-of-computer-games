$(function () {
    var $zip = $('[name="zip"]'),
        $region = $('[name="region"]'),
        $district = $('[name="district"]'),
        $city = $('[name="city"]'),
        $street = $('[name="street"]'),
        $building = $('[name="building"]');

    var $tooltip = $('.tooltip');

    $.fias.setDefault({
        parentInput: '.js-form-address',
        verify: true,
        select: function (obj) {
            setLabel($(this), obj.type);
            $tooltip.hide();
        },
        check: function (obj) {
            var $input = $(this);
            validateAddress(obj, function(result) {
                if (result) {
                    setLabel($input, result.type);
                    $tooltip.hide();
                } else {
                    showError($input, 'Введено неверно');
                }
            });
        },
        checkBefore: function () {
            var $input = $(this);
            if (!$.trim($input.val())) {
                $tooltip.hide();
                return false;
            }
        },
        change: function (obj) {
            if (obj && obj.parents) {
                $.fias.setValues(obj.parents, '.js-form-address');
            }
            if (obj && obj.zip) {
                $('[name="zip"]').val(obj.zip);
            }
        },
    });

    // Инициализация полей
    initializeFiasFields();

    function initializeFiasFields() {
        // Настройка типов
        $region.fias('type', $.fias.type.region);
        $district.fias('type', $.fias.type.district);
        $city.fias('type', $.fias.type.city);
        $street.fias('type', $.fias.type.street);
        $building.fias('type', $.fias.type.building);

        // Включаем родительские связи
        $district.fias('withParents', true);
        $city.fias('withParents', true);
        $street.fias('withParents', true);

        // Отключаем проверку для строений
        $building.fias('verify', false);
        
        // Инициализация для однострочного ввода адреса
        $('[name="address"]').fias({
            oneString: true,
            parentInput: '.js-form-address',
            verify: true,
            select: function (obj) {
                setLabel($(this), obj.type);
                $tooltip.hide();
                // Обновление других полей при выборе города
                updateAddressFields(obj);
            },
            checkBefore: function () {
                var $input = $(this);
                if (!$.trim($input.val())) {
                    return false;
                }
                return true;
            },
            change: function (obj) {
                if (obj && obj.parents) {
                    $.fias.setValues(obj.parents, '.js-form-address');
                }
                if (obj && obj.zip) {
                    $('[name="zip"]').val(obj.zip);
                }
            },
        });
    }

    function validateAddress(query, callback) {
        // Здесь вы можете реализовать логику проверки существования объекта.
        // Например, отправить AJAX-запрос на сервер или проверить локально.
        
        // Пример проверки:
        var mockDatabase = [
            { id: 1, name: 'Москва' },
            { id: 2, name: 'Санкт-Петербург' },
            // Добавьте другие объекты по мере необходимости
        ];

        var result = mockDatabase.find(item => item.name === query.name); // Пример поиска по имени

        if (result) {
            callback(result); // Если объект найден, передаем его в коллбек
        } else {
            callback(false); // Если не найден, передаем false
        }
    }

    function updateAddressFields(obj) {
       // Обновление значений полей на основе выбранного объекта
       if (obj.region) $('[name="region"]').val(obj.region);
       if (obj.district) $('[name="district"]').val(obj.district);
       if (obj.city) $('[name="city"]').val(obj.city);
       if (obj.street) $('[name="street"]').val(obj.street);
       if (obj.building) $('[name="building"]').val(obj.building);
   }

   function setLabel($input, text) {
       text = text.charAt(0).toUpperCase() + text.substr(1).toLowerCase();
       $input.parent().find('label').text(text);
   }

   function showError($input, message) {
       $tooltip.find('span').text(message);

       var inputOffset = $input.offset(),
           inputWidth = $input.outerWidth(),
           inputHeight = $input.outerHeight();

       var tooltipHeight = $tooltip.outerHeight();

       $tooltip.css({
           left: (inputOffset.left + inputWidth + 10) + 'px',
           top: (inputOffset.top + (inputHeight - tooltipHeight) / 2 - 1) + 'px'
       });

       $tooltip.show();
   }
});
