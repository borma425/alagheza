function queryParamExistUrl(param = '') {
    param_value = new URLSearchParams(window.location.search).get(param);
    if (param_value != null){
     return param_value
    }else{
     return false
    }
}
