$(function () {
    var $address = $('[name="address"]'),
        $parent = $('[name="parent"]');

    $address.fias({
        oneString: true,
        change: function (obj) {
            log(obj);
        }
    });

    $parent.change(function () {
        changeParent($(this).val());
    });

    changeParent($('[name="parent"]:checked').val());

    function changeParent(value) {
        var parentType = null,
            parentId = null;

        switch (value) {
            case 'moscow':
                parentType = $.fias.type.region;
                parentId = '7700000000000';
                break;

            case 'petersburg':
                parentType = $.fias.type.region;
                parentId = '7800000000000';
                break;
        }

        $address.fias({
            parentType: parentType,
            parentId: parentId
        });
    }

    function log(obj) {
        var $log, i;

        $('.js-log li').hide();

        for (i in obj) {
            $log = $('#' + i);

            if ($log.length) {
                $log.find('.value').text(obj[i]);
                $log.show();
            }
        }
    }

    // Добавляем проверку адреса
    $('form').on('submit', function (e) {
        e.preventDefault(); // Останавливаем стандартное поведение формы
        const address = $address.val();

        checkAddress(address).then(isValid => {
            if (isValid) {
                this.submit(); // Отправляем форму, если адрес валиден
            } else {
                alert('Указанный адрес не найден в базе данных. Пожалуйста, проверьте введённый адрес.');
            }
        }).catch(err => {
            alert('Произошла ошибка при проверке адреса. Пожалуйста, попробуйте снова.');
            console.error(err);
        });
    });

    async function checkAddress(address) {
        try {
            const response = await fetch('validate_address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ address })
            });

            const data = await response.json();
            return data.valid;
        } catch (err) {
            console.error('Ошибка проверки адреса:', err);
            return false;
        }
    }
});
