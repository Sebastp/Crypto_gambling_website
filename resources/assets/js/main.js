window.collectionHas = function(a, b) { //helper function (see below)
  for(var i = 0, len = a.length; i < len; i ++) {
    if(a[i] == b) return true;
  }
  return false;
}

window.findParentBySelector = function(elm, selector){
    if (selector[0] == '#') {
      var all = [document.querySelector("[id='"+selector.slice( 1 )+"']")];
      if (all == null) {
        return null;
      }
    }else {
      var all = document.querySelectorAll(selector);
    }

    var cur = elm.parentNode;
    while(cur && !collectionHas(all, cur)) { //keep going up until you find a match
        cur = cur.parentNode; //go up
    }
    return cur; //will return null if not found
}


window.isElemOrChild = function(elm, selector) {
  if (selector[0] == '#') {
    if (elm.id == selector.slice( 1 )) {
      var isElem = 1;
    }else {
      var isElem = 0;
    }
  }else if(selector[0] == '.'){
    if (typeof elm.className != 'object' && typeof elm.className != 'undefined' && elm.className.includes(selector.slice( 1 ))) {
      var isElem = 1;
    }else {
      var isElem = 0;
    }
  }else if(selector[0] == '['){
    // trimedSelector = selector.substring(0, selector.length - 2);
    trimedSelector = selector.replace(/[\[\]']+/g,'');
    if (elm.hasAttribute(trimedSelector)) {
      var isElem = 1;
    }else {
      var isElem = 0;
    }
  }else {
    if (elm.tagName == selector.toUpperCase()) {
      var isElem = 1;
    }else {
      var isElem = 0;
    }
  }

  if (isElem) {
    return 1;
  }else {
    var parRet = findParentBySelector(elm, selector);
    if (parRet != null) {
      return 1;
    }else {
      return 0;
    }
  }
}


window.getTransfParams = function(transformStr) {
  var GElemTransform = Array.from(transformStr.replace('translate(', '').slice(0, -1).split(", "));

  return [parseInt(GElemTransform[0]), parseInt(GElemTransform[1])];
}

window.showAlertModal = function(msg) {
  alert(msg);
  $('.modal-alert .modal-mainMsg').text(msg);
  $('.modal-alert').click();
}


$(function () {
  $(window).scroll(function () {
    var leftOffset = parseInt($(".topbar").css('left'));
    $('.topbar').css({
      'left': $(this).scrollLeft()*-1 //Use it later
    });


    if ($('.topbar')[0].hasAttribute('data-shadow')) {
      if ($(this).scrollTop() == 0) {
        $('.topbar').addClass('topbar-no_shadow');
      } else {
        $('.topbar').removeClass('topbar-no_shadow');
      }
    }
  });


  $('[label-onfcs]').blur(function() {
    var inptVal = $(this).val(),
        labelElm = $(this).parent().siblings('.inpt-label__float');
    if (inptVal == '') {
      labelElm.removeClass('label__float-active');
      labelElm.removeClass('label__float-blur');
    }else {
      labelElm.addClass('label__float-blur')
    }
  });

  $('[label-onfcs]').focus(function() {
    var inptVal = $(this).val(),
        labelElm = $(this).parent().siblings('.inpt-label__float');
    if (inptVal != '') {
      labelElm.removeClass('label__float-blur');
    }else {
      labelElm.addClass('label__float-blur')
    }
  });


  $('[label-onfcs]').keydown(function() {
    var that = $(this),
        labelElm = that.parent().siblings('.inpt-label__float');

    setTimeout( function() {
      var inptVal = that.val();
      if (inptVal != '') {
        labelElm.removeClass('label__float-blur');
        that.parent().siblings('.inpt-label__float').addClass('label__float-active');
      }
    }, 10);
  });

  $(window).click(function(el) {
    if (!isElemOrChild(el.target, '.topbar-nav') && !isElemOrChild(el.target, '.topbar-nav-icon')) {
      $('.topbar-nav[style="display: flex;"]').hide();
    }
  });

  $('[nav-action]').click(function() {
    var navID = $(this).attr('nav-action');
    if ($('[nav-id="'+navID+'"]')[0].style.display == 'none' || $('[nav-id="'+navID+'"]')[0].style.display == '') {
      $('[nav-id="'+navID+'"]')[0].style.display = "flex";
    }else {
      $('[nav-id="'+navID+'"]')[0].style.display = "none";
    }
  });



  $('[modal-action]').click(function() {
    $('.modal-window').removeClass('modalActive');
    var modalID = $(this).attr('modal-action');
    $('body').addClass('modalMode');
    $('[modal-id="'+modalID+'"]').addClass('modalActive');
  });


  $('.modal-bck, .modalClose').click(function() {
    $(findParentBySelector($(this)[0], '.modal-window')).removeClass('modalActive');
    $('body').removeClass('modalMode');
  });


  $('.inpt-select').click(function() {
    $(this).select();
  });

  $('[copy-btn]').click(function() {
    var copyVal = $('#'+$(this).attr('copy-btn'));
    copyVal.select();
    try {
      var successful = document.execCommand('copy');
      if (successful) {
        $(this).text('Copied');
      }
    } catch (err) {
      showAlertModal('Unable to copy');
    }
  });





  $('#landing-nick-action').click(function() {
    $('.landing-nick__show').hide();
    $('#landing-nick-edit').show();

    $('#landing-nick__inpt').focus();
  });

  $('#landing-nick__inpt').keydown(function(event) {
    var thisScope = $(this);
    setTimeout( function() {
      $('.nickname-text').text(thisScope.val());
    }, 10);
  });


  $('#landing-nick__inpt').blur(function() {
    $('.landing-nick__show').show();
    $('#landing-nick-edit').hide();
  });


  $('#landing-url__show').click(function() {
    $(this).hide();
    $('#landing-usr__urlcontent').text($(this).attr('data-fullurl'));
  });


});
