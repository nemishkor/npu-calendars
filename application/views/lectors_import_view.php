<h1><i class="uk-icon-file-excel-o"></i> Імпорт викладачів</h1>
<div class="uk-grid">
    <div class="uk-width-1-2">
        <div class="uk-form uk-form-horizontal">
            <div class="uk-form-row">
                <label class="uk-form-label" for="file-input">
                    Виберіть csv файл
                </label>
                <div class="uk-form-controls">
                    <input type="file" id="file-input" />
                </div>
            </div>
            <div class="uk-form-row">
                <label class="uk-form-label" for="field_delimiter">
                    Символ розділення полів
                </label>
                <div class="uk-form-controls">
                    <input type="text" id="field_delimiter" name="field_delimiter" value=",">
                </div>
            </div>
            <div class="uk-form-row">
                <label class="uk-form-label" for="text_delimiter">
                    Символ позначення тексту
                </label>
                <div class="uk-form-controls">
                    <input type="text" id="text_delimiter" name="text_delimiter" value="&quot;">
                </div>
            </div>
        </div>
        <div class="import-toolbar uk-margin uk-button-group" style="display: none;">
            <button class="uk-button uk-button-success"><i class="uk-icon-plus"></i> Додати</button>
            <button id="split-name"
                    class="uk-button"
                    data-uk-tooltip
                    title="Дані в полі &quot;Ім'я&quot; розділяться на 3 поля: &quot;Ім'я&quot;, &quot;Призвіще&quot;, &quot;По батькові&quot;">
                Розділити ПІБ
            </button>
        </div>
        <table id="imported-data" class="uk-margin" style="display: none;">
            <thead>
            <tr class="fields">
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        <h3>Contents of the file:</h3>
        <pre id="file-content"></pre>
    </div>
</div>


<script>
    function readSingleFile(e) {
        var file = e.target.files[0];
        if (!file) {
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            var contents = e.target.result;
            displayContents(contents);
        };
        reader.readAsText(file);
    }

    function displayContents(contents) {
        var fieldDelimiter = $('#field_delimiter').val();
        var textDelimiter = $('#text_delimiter').val();
        var str = contents;
        var rows = [];
        var next = true;
        while(next){
            var row = [];
            for(var i = 0; i < 4; i++){
                var fieldDelimiterPos = str.indexOf(fieldDelimiter);
                var textDelimiterPos = str.indexOf(textDelimiter);
                var endRowPos = str.indexOf("\n");
                if(endRowPos == -1 && textDelimiterPos == -1 && fieldDelimiterPos == -1)
                    next = false;
                if(endRowPos < textDelimiterPos && endRowPos < fieldDelimiterPos)
                    break;
                if(textDelimiterPos < fieldDelimiterPos){
                    textDelimiterPos = str.indexOf(textDelimiter, 1);
                    row.push(str.substring(1, textDelimiterPos));
                    str = str.substring(textDelimiterPos + 2);
                } else {
                    row.push(str.substring(0, fieldDelimiterPos));
                    str = str.substring(fieldDelimiterPos + 1);
                }
            }
            rows.push(row);
        }
        var form = $('#imported-data');
        form.find("tbody").html("");
        for(var i = 0; i < rows.length; i++){
            var tr = $("<tr></tr>");
            for(var j = 0; j < rows[i].length; j++){
                tr.append("<td>" + rows[i][j] + "</td>");
            }
            form.find("tbody").append(tr);
        }
        form.add('.import-toolbar').show(400);
        var element = document.getElementById('file-content');
        element.innerHTML = contents;
    }

    var fields = [
        {name: "name",          label: "Ім'я",          show: true},
        {name: "published",     label: "Увім./викм.",   show: false},
        {name: "institute",     label: "Інститут",      show: false},
        {name: "surname",       label: "Призвіще",      show: true},
        {name: "lastname",      label: "По батькові",   show: true},
        {name: "link",          label: "Web посилання", show: true},
        {name: "gender",        label: "Стать",         show: false},
        {name: "description",   label: "Опис",          show: true}
    ];

    function update_table_fields() {
        if($('#imported-data .fields').children().length == 0){
            var options = [];
            for(var i = 0; i < fields.length; i++){
                if(fields[i].show == false){
                    options.push(i);
                }
            }
            var firstFields = "";
            var secondFields = "";
            for(var i = 0; i < fields.length; i++){
                var field = $('<th><select></select></th>');
                console.log(field.html());
                if(fields[i].show) {
                    field.find('select')
                        .append('<option value="' + fields[i].name + '" selected>' + fields[i].label + '</option>');
                } else {
                    field.find('select')
                        .append('<option value="null" selected>-= немає поля =-</option>');
                }
                for (var j = 0; j < options.length; j++) {
                    field.find('select')
                        .append('<option value="' + fields[options[j]].name + '">' + fields[options[j]].label + '</option>');
                }
                if(fields[i].show) {
                    field.find('select')
                        .append('<option value="null">-= немає поля =-</option>');
                    firstFields += field[0].outerHTML;
                } else {
                    secondFields += field[0].outerHTML;
                }
            }
            $('#imported-data .fields').append(firstFields + secondFields);
        } else {
            var obj = $(this);
            var newValue = $(this).val();
            var newValueIndex;
            var oldValueIndex = null;
            // deleting option with OLD value and change 'show' parameter
            obj.find('option').each(function(index, dom){
                console.log('option = ' + $(dom).html());
                for(var i = 0; i < fields.length; i++){
                    if(fields[i].show == true && $(dom).val() == fields[i].name) {
                        oldValueIndex = i;
                        $(dom).remove();
                        fields[i].show = false;
                    }
                }
            });
            if(obj.val() != 'null'){
                // change 'show' parameters for NEW fields
                obj.find('option').each(function(index, dom) {
                    for (var i = 0; i < fields.length; i++) {
                        if (fields[i].name == newValue) {
                            newValueIndex = i;
                            fields[i].show = true;
                        }
                    }
                });
                // deleting options with new value from all select tags
                $('#imported-data .fields th select').each(function(index, dom){
                    if(obj[0] != $(dom)[0]) {
                        $(dom).find('option').each(function (index2, dom2) {
                            for (var i = 0; i < fields.length; i++) {
                                if ($(dom2).attr('value') == fields[newValueIndex].name)
                                    $(dom2).remove();
                            }
                        });
                    }
                });
            }
            if(oldValueIndex != null) {
                var oldOption = '<option value="' + fields[oldValueIndex].name + '">' + fields[oldValueIndex].label + '</option>';
                $('#imported-data .fields th select').append(oldOption);
            }
        }
        console.log(fields);
    }

    $(document).ready(function(){
        update_table_fields();
        $('#file-input').on('change', readSingleFile);
        $('#imported-data thead select').on('change', update_table_fields);
        $('#split-name').click(function(){
            var success = true;
            var nameCol = null;
            var surnameCol = null;
            var lastnameCol = null;
            $('#imported-data .fields select').each(function(index, dom){
                var option = $(dom).find('option:selected');
                if(option.attr('value') == 'name')
                    nameCol = index;
                if(option.attr('value') == 'surname')
                    surnameCol = index;
                if(option.attr('value') == 'lastname')
                    lastnameCol = index;
            });
            if(nameCol == null && surnameCol == null && lastnameCol == null)
                success = false;
            else {
                $('#imported-data tbody tr').each(function(){
                    var str = $(this).find('td').eq(nameCol);
                    if($(this).find('td').eq(surnameCol).val() == '' &&
                       $(this).find('td').eq(lastnameCol).val() == ''){
                        var surNamePos  = str.indexOf(' ');
                        var lastNamePos = str.indexOf(' ', surNamePos + 1);
                        var name        = str.substring(0, surNamePos);
                        var surName     = str.substring(surNamePos, lastNamePos);
                        var lastName    = str.substr(lastNamePos);
                        console.log("name = " + name);
                        console.log("surname = " + surName);
                        console.log("lastname = " + lastName);
                        if(surName && lastName){
                            $(this).find('td').eq(surnameCol).val(surName);
                            $(this).find('td').eq(lastnameCol).val(lastName);
                        }
                    }
                });
            }
            if(success)
                $(this).attr('disabled', '').addClass('uk-disabled');
        });
    });
</script>