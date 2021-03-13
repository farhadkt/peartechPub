$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("input[data-bootstrap-switch]").each(function () {
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

    $("a[data-widget='pushmenu']").on('click', function () {
        if ($("body").hasClass('sidebar-collapse')) {
            $("#select-group-product").css("display", "flex");
            $("#select-group-product").css("flex-direction", "column");

            $("#groups-list").css("order", "1");
            $("#products-list").css("order", "2");
        } else if (!$("body").hasClass('sidebar-collapse')) {
            $("#select-group-product").css("display", "");
            $("#select-group-product").css("flex-direction", "");

            $("#groups-list").css("order", "");
            $("#products-list").css("order", "");
        }

        if ($(window).width() == 976) {
            setCookie('ui_sc', "0");
        } else {
            if ($("body").hasClass('sidebar-collapse')) {
                setCookie('ui_sc', "1");
            } else if (!$("body").hasClass('sidebar-collapse')) {
                setCookie('ui_sc', "0");
            }
        }
    });

    $('[data-toggle="tooltip"]').tooltip();

    // $('#delivery_date').daterangepicker({
    //     singleDatePicker: true,
    //     showDropdowns: true,
    //     minYear: 2020,
    //     locale: {
    //         format: 'YYYY-MM'
    //     }
    // });

});

function currencyMask() {
    $('.js_currency:not(th)').inputmask({
        alias: "currency",
        prefix: '',
        suffix: ' CAD',
        rightAlign: false,
        autoUnmask: true,
        removeMaskOnSubmit: true,
    });
}

function calculateCommission(amount) {
    return amount * parseFloat($("[name=vc_commission_const]").val());
}

function currencyFormat(amount) {
    return new Intl.NumberFormat('en-CA', {
        style: 'currency',
        currency: 'CAD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

function showCommission($element, amount) {
    $element.html(
        currencyFormat(calculateCommission(amount))
    );
}

function showCollateralAmount(amount, percent, $target) {
    if (!amount) { amount = 0; }
    var result = amount * percent * 0.01;
    $target.val(currencyFormat(result));
}

function showCollateralPercent(totalAmount, amount, $target) {
    amount = parseFloat(amount.replace('$', ''));
    if (!totalAmount) { totalAmount = 0; }
    var result = (amount * 100) / totalAmount;
    $target.val(result);
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999;';
}

function truncate(str, n) {
    return (str.length > n) ? str.substr(0, n - 1) + '&hellip;' : str;
}

function parseISOStringToDate(s) {
    var b = s.split(/\D+/);
    return new Date(Date.UTC(b[0], --b[1], b[2], b[3], b[4], b[5], b[6]));
}

// symbol icons :
var productSymbols = [
    {
        name: 'Energy and petroleum products',
        symbol: '<i class="fas w-i fa-fire-alt energy-color"></i>'
    },
    {
        name: 'Meat, fish, and dairy products',
        symbol: '<i class="fas w-i fa-fish fish-color"></i>'
    },
    {
        name: 'Fruit, vegetables, feed and other food products',
        symbol: '<i class="fas w-i fa-apple-alt fruit-color"></i>'
    },
    {
        name: 'Beverages (except juices)',
        symbol: '<i class="fas w-i fa-cocktail beverages-color"></i>'
    },
    {
        name: 'Tobacco products',
        symbol: '<i class="fas w-i fa-smoking smoke-color"></i>'
    },
    {
        name: 'Textile and leather products',
        symbol: '<i class="fas w-i fa-horse-head textile-color"></i>'
    },
    {
        name: 'Clothing, footwear and accessories',
        symbol: '<i class="fas w-i fa-tshirt cloth-color"></i>'
    },
    {
        name: 'Chemicals and chemical products',
        symbol: '<i class="fas w-i fa-bong chemical-color"></i>'
    },
    {
        name: 'Plastic and rubber products',
        symbol: '<i class="fas w-i fa-ring plastic-color"></i>'
    },
    {
        name: 'Lumber and other wood products',
        symbol: '<i class="fas w-i fa-tree lumber-color"></i>'
    },
    {
        name: 'Pulp and paper products',
        symbol: '<i class="fas w-i fa-scroll pulp-color"></i>'
    },
    {
        name: 'Primary ferrous metal products',
        symbol: '<i class="fas w-i fa-drum-steelpan ferrous-color"></i>'
    },
    {
        name: 'Primary non-ferrous metal products',
        symbol: '<i class="fas w-i fa-tape non-ferrous-color"></i>'
    },
    {
        name: 'Fabricated metal products and construction materials',
        symbol: '<i class="fas w-i fa-building fabricated-color"></i>'
    },
    {
        name: 'Motorized and recreational vehicles',
        symbol: '<i class="fas w-i fa-motorcycle motorized-color"></i>'
    },
    {
        name: 'Machinery and equipment',
        symbol: '<i class="fas w-i fa-tools machinery-color"></i>'
    },
    {
        name: 'Electrical, electronic, audiovisual and telecommunication products',
        symbol: '<i class="fas w-i fa-plug electronic-color"></i>'
    },
    {
        name: 'Furniture and fixtures',
        symbol: '<i class="fas w-i fa-couch furniture-color"></i>'
    },
    {
        name: 'Cement, glass, and other non-metallic mineral products',
        symbol: '<i class="fas w-i fa-dolly-flatbed glass-color"></i>'
    },
    {
        name: 'Packaging materials and containers',
        symbol: '<i class="fas w-i fa-box-open pack-color"></i>'
    },
    {
        name: 'Miscellaneous products',
        symbol: '<i class="fas w-i fa-cube miscellaneous-color"></i>'
    },

];
