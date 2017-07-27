$(document).ready(function () {
    $('#newValue').click(function () {
        $('#valueWrapper').append('' +
            '              <dl>\n' +
            '                    <dt>\n' +
            '                        <label for="k_attributwert">Attributname</label>\n' +
            '                    </dt>\n' +
            '                    <dd>\n' +
            '                        <input name="attributevalues[][name]" id="k_attributwert">\n' +
            '                    </dd>\n' +
            '                </dl>');
    });
});