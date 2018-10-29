function ChangeValue(index) {
    var _id = index.id;
    if (index.value == '') {
        $('#' + _id).tooltip("show");
    } else {
        $('#' + _id).tooltip("destroy");
    }
}

function FormDataEmptyTooltip(obj) {
    var dataType = obj.type;
    switch (dataType) {
        case "text":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                obj.focus();
                return true;
            }
            return false;
        case "password":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                obj.focus();
                return true;
            }
            return false;
        case "textarea":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                obj.focus();
                return true;
            }
            return false;
        case "time":
            obj.value.replace(' ', '');
            if (obj.value == "") {
                obj.focus();
                return true;
            }
            return false;
        case "select-one":
            if (obj.value == '') {
                obj.focus();
                return true;
            }
            return false;
        case "select-multiple":
            if (!obj.selected) {
                obj.focus();
                return true;
            }
            return false;
        case "hidden":
            obj.value.replace(' ', '');
            if (obj.value.length == 0) {
                return true;
            }
            return false;
        case "file":
            if (obj.files.length == 0) {
                return true;
            }
            return false;
        case 'checkbox':
            if (!obj.checked) {
                return true;
            }
            return false;
        case 'radio':
            if (!obj.checked) {
                return true;
            }
            return false;
        default:
            if (obj.length > 0) {
                MyType = obj[0].type;
                switch (MyType) {
                    case 'checkbox':
                        for (var i = 0; i < obj.length; i++) {
                            if (obj[i].checked) {
                                return false;
                                break;
                            }
                        }
                        return true;
                        break;
                    case 'radio':
                        for (var i = 0; i < obj.length; i++) {
                            if (obj[i].checked) {
                                return false;
                                break;
                            }
                        }
                        return true;
                        break;
                    default:
                        return true;
                        break;
                }
                return true;
            } else {
                alert("不支援此輸入元件");
                return true;
            }
            break;
    }
}