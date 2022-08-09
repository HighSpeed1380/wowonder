jQuery(document).ready(function ($) {
  try {
    window.AudioContext = window.AudioContext || window.webkitAudioContext;
    navigator.getUserMedia = navigator.getUserMedia
      || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
    window.URL = window.URL || window.webkitURL;

  }
  catch (e) {
    console.log('There is no support audio in this browser');
  }
  $(document).on('click', "#recordPostAudio", function (event) {
    audio_context = new AudioContext;
    var _SELF = $(this);
    if (!localstream) {
      Wo_CreateUserMedia();
    }
    Wo_Delay(function () {
      if (localstream && recorder && _SELF.attr('data-record') == 0 && Wo_IsRecordingBufferClean()) {
        Wo_CleanRecordNodes();
        recording_time = $('#postRecordingTime');
        recording_node = "post";
        _SELF.attr('data-record', '1').html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-stop-circle main"><circle cx="12" cy="12" r="10"></circle><rect x="9" y="9" width="6" height="6"></rect></svg>');
        Wo_startRecording();
      }
      else if (localstream && recorder && _SELF.attr('data-record') == 1 && $("[data-record='1']").length == 1) {
        Wo_stopRecording();
        _SELF.html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>').attr('data-record', '2');
      }
      else if (localstream && recorder && _SELF.attr('data-record') == 2) {
        Wo_CleanRecordNodes();
        Wo_StopLocalStream();
        _SELF.html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mic" color="#009da0"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>').attr('data-record', '0');
      }
      else {
        return false;
      }
    }, 500);
  });

  $(document).on('click', ".record-comment-audio", function (event) {
    audio_context = new AudioContext;
    var _SELF = $(this);
    if (!localstream) {
      Wo_CreateUserMedia();
    }
    Wo_Delay(function () {
      if (recorder && _SELF.attr('data-record') == 0 && Wo_IsRecordingBufferClean()) {
        Wo_CleanRecordNodes();
        recording_time = $("span[data-comment-rtime='" + _SELF.attr('id') + "']");
        recording_node = "comm";
        comm_field = _SELF.attr('id');
        _SELF.attr('data-record', '1').html('<svg enable-background="new 0 0 64 64" height="512" viewBox="0 0 64 64" width="512" xmlns="http://www.w3.org/2000/svg"><g><g><path d="m32 60.5c-15.715 0-28.5-12.785-28.5-28.5s12.785-28.5 28.5-28.5 28.5 12.785 28.5 28.5-12.785 28.5-28.5 28.5zm0-54c-14.061 0-25.5 11.439-25.5 25.5s11.439 25.5 25.5 25.5 25.5-11.439 25.5-25.5-11.439-25.5-25.5-25.5z"/></g><g><path d="m42 45.5h-20c-.935 0-1.813-.364-2.475-1.025s-1.025-1.54-1.025-2.475v-20c0-.935.364-1.813 1.025-2.475s1.54-1.025 2.475-1.025h20c.935 0 1.813.364 2.475 1.025s1.025 1.54 1.025 2.475v20c0 .935-.364 1.813-1.025 2.475s-1.54 1.025-2.475 1.025zm-20-24c-.131 0-.26.053-.354.146-.094.095-.146.221-.146.354v20c0 .132.053.26.146.354.094.092.223.146.354.146h20c.132 0 .26-.054.354-.146.092-.094.146-.222.146-.354v-20c0-.133-.052-.259-.146-.354-.094-.093-.222-.146-.354-.146z"/></g></g></svg>');
        Wo_startRecording();
      }

      else if (recorder && _SELF.attr('data-record') == 1 && $("[data-record='1']").length == 1) {
        Wo_stopRecording();
        _SELF.html('<svg height="512pt" viewBox="0 0 512 512" width="512pt" xmlns="http://www.w3.org/2000/svg"><path d="m256 512c-141.164062 0-256-114.835938-256-256s114.835938-256 256-256 256 114.835938 256 256-114.835938 256-256 256zm0-480c-123.519531 0-224 100.480469-224 224s100.480469 224 224 224 224-100.480469 224-224-100.480469-224-224-224zm0 0"/><path d="m176.8125 351.1875c-4.097656 0-8.195312-1.554688-11.308594-4.691406-6.25-6.25-6.25-16.382813 0-22.632813l158.398438-158.402343c6.253906-6.25 16.386718-6.25 22.636718 0s6.25 16.382812 0 22.636718l-158.402343 158.398438c-3.15625 3.136718-7.25 4.691406-11.324219 4.691406zm0 0"/><path d="m335.1875 351.1875c-4.09375 0-8.191406-1.554688-11.304688-4.691406l-158.398437-158.378906c-6.253906-6.25-6.253906-16.382813 0-22.632813 6.25-6.253906 16.382813-6.253906 22.632813 0l158.398437 158.398437c6.253906 6.25 6.253906 16.382813 0 22.632813-3.132813 3.117187-7.230469 4.671875-11.328125 4.671875zm0 0"/></svg>').attr('data-record', '2');
      }

      else if (recorder && _SELF.attr('data-record') == 2) {
        Wo_CleanRecordNodes();
        Wo_StopLocalStream();
        _SELF.html('<svg enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><path d="m416.616 218.033v-37.417c0-8.284-6.716-15-15-15s-15 6.716-15 15v37.417c0 72.022-58.594 130.616-130.616 130.616s-130.616-58.594-130.616-130.616v-37.417c0-8.284-6.716-15-15-15s-15 6.716-15 15v37.417c0 83.506 64.06 152.32 145.616 159.91v42.357h-56.999c-35.675 0-64.7 29.024-64.7 64.7 0 14.888 12.112 27 27 27h219.396c14.888 0 27-12.112 27-27 0-35.676-29.024-64.7-64.7-64.7h-56.997v-42.358c81.556-7.589 145.616-76.404 145.616-159.909zm-54.046 263.967h-213.14c1.525-17.735 16.448-31.7 34.571-31.7h143.997c18.124 0 33.046 13.965 34.572 31.7z"/><path d="m256 318.649c55.48 0 100.616-45.136 100.616-100.616v-117.417c0-55.48-45.136-100.616-100.616-100.616s-100.616 45.136-100.616 100.616v117.416c0 55.481 45.136 100.617 100.616 100.617zm0-288.649c33.79 0 62.099 23.862 68.997 55.616h-34.613c-8.284 0-15 6.716-15 15s6.716 15 15 15h36.232v30h-36.232c-8.284 0-15 6.716-15 15s6.716 15 15 15h36.232v30h-36.232c-8.284 0-15 6.716-15 15s6.716 15 15 15h34.016c-7.835 30.459-35.53 53.033-68.399 53.033s-60.565-22.574-68.399-53.033h32.996c8.284 0 15-6.716 15-15s-6.716-15-15-15h-35.213v-30h35.213c8.284 0 15-6.716 15-15s-6.716-15-15-15h-35.213v-30h35.213c8.284 0 15-6.716 15-15s-6.716-15-15-15h-33.594c6.897-31.754 35.206-55.616 68.996-55.616z"/></svg>').attr('data-record', '0');
      }

      else {
        return false;
      }
    }, 500);

  });

  $(document).on('click', ".record-chat-audio", function (event) {
    audio_context = new AudioContext;
    var _SELF = $(this);
    if (!localstream) {
      Wo_CreateUserMedia();
    }
    Wo_Delay(function () {
      if (recorder && _SELF.attr('data-record') == 0 && Wo_IsRecordingBufferClean() && $("[data-record='1']").length == 0) {
        Wo_CleanRecordNodes();
        recording_time = $("span[data-chat-rtime='" + _SELF.attr('data-chat-tab') + "']");
        recording_node = "chat";
        chat_tab = _SELF.attr('data-chat-tab');
        _SELF.attr('data-record', '1').html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"  width="24" height="24" viewBox="0 0 24 24" class="select-color"><path fill="#a84849" d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M9,9H15V15H9" /></svg>');
        Wo_startRecording();
      }

      else if (recorder && _SELF.attr('data-record') == 1 && $("[data-record='1']").length == 1) {
        Wo_stopRecording();
        _SELF.html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"  width="24" height="24" viewBox="0 0 24 24" class="select-color"><path fill="#a84849" d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" /></svg>').attr('data-record', '2');
      }

      else if (recorder && _SELF.attr('data-record') == 2) {
        Wo_CleanRecordNodes();
        Wo_StopLocalStream();
        _SELF.html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"  width="24" height="24" viewBox="0 0 24 24" class="select-color"><path fill="#a84849" d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z" /></svg>').attr('data-record', '0');
      }

      else {
        return false;
      }
      if (_SELF.parents('form').length > 0 && _SELF.parents('form').find('#color').length > 0) {
        _SELF.find('[fill]').attr('fill', _SELF.parents('form').find('#color').val());
      }
    }, 500);

  });

  $(document).on('click', "#messages-record", function (event) {
    audio_context = new AudioContext;
    var _SELF = $(this);
    if (!localstream) {
      Wo_CreateUserMedia();
    }
    Wo_Delay(function () {
      if (recorder && _SELF.attr('data-record') == 0 && Wo_IsRecordingBufferClean() && $("[data-record='1']").length == 0) {
        Wo_CleanRecordNodes();
        recording_time = $("span.messages-rtime");
        recording_node = "msg";
        _SELF.attr('data-record', '1').html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-stop-circle main"><circle cx="12" cy="12" r="10"></circle><rect x="9" y="9" width="6" height="6"></rect></svg>');
        Wo_startRecording();
      }

      else if (recorder && _SELF.attr('data-record') == 1 && $("[data-record='1']").length == 1) {
        Wo_stopRecording();
        _SELF.html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>').attr('data-record', '2');
      }

      else if (recorder && _SELF.attr('data-record') == 2) {
        Wo_CleanRecordNodes();
        Wo_StopLocalStream();
        _SELF.html('<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24"><path fill="#ff3a55" d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z"></path></svg>').attr('data-record', '0');
      }

      else {
        return false;
      }
    }, 500);

  });
});

function Wo_IsRecordingBufferClean() {
  return $("[data-record='1']").length == 0;
}

function Wo_CreateUserMedia() {
  navigator.getUserMedia({ audio: true }, Wo_startUserMedia, function (e) {
    console.log('Could not get input or something went wrong: ' + e);
  });
}
function Wo_CleanRecordNodes(color = "#cf8283") {
  $(".record-comment-audio").each(function (index, el) {
    $(el).html('<svg enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><path d="m416.616 218.033v-37.417c0-8.284-6.716-15-15-15s-15 6.716-15 15v37.417c0 72.022-58.594 130.616-130.616 130.616s-130.616-58.594-130.616-130.616v-37.417c0-8.284-6.716-15-15-15s-15 6.716-15 15v37.417c0 83.506 64.06 152.32 145.616 159.91v42.357h-56.999c-35.675 0-64.7 29.024-64.7 64.7 0 14.888 12.112 27 27 27h219.396c14.888 0 27-12.112 27-27 0-35.676-29.024-64.7-64.7-64.7h-56.997v-42.358c81.556-7.589 145.616-76.404 145.616-159.909zm-54.046 263.967h-213.14c1.525-17.735 16.448-31.7 34.571-31.7h143.997c18.124 0 33.046 13.965 34.572 31.7z"/><path d="m256 318.649c55.48 0 100.616-45.136 100.616-100.616v-117.417c0-55.48-45.136-100.616-100.616-100.616s-100.616 45.136-100.616 100.616v117.416c0 55.481 45.136 100.617 100.616 100.617zm0-288.649c33.79 0 62.099 23.862 68.997 55.616h-34.613c-8.284 0-15 6.716-15 15s6.716 15 15 15h36.232v30h-36.232c-8.284 0-15 6.716-15 15s6.716 15 15 15h36.232v30h-36.232c-8.284 0-15 6.716-15 15s6.716 15 15 15h34.016c-7.835 30.459-35.53 53.033-68.399 53.033s-60.565-22.574-68.399-53.033h32.996c8.284 0 15-6.716 15-15s-6.716-15-15-15h-35.213v-30h35.213c8.284 0 15-6.716 15-15s-6.716-15-15-15h-35.213v-30h35.213c8.284 0 15-6.716 15-15s-6.716-15-15-15h-33.594c6.897-31.754 35.206-55.616 68.996-55.616z"/></svg>').attr('data-record', '0');
    $('[data-comment-rtime="' + $(el).attr('id') + '"]').text('00:00').addClass('hidden');
  });

  $(".record-chat-audio").each(function (index, el) {
	  $(el).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"  width="24" height="24" viewBox="0 0 24 24" class="select-color"><path fill="#a84849" d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z" /></svg>').attr('data-record', '0');
    $('[data-chat-rtime="' + $(el).attr('data-chat-tab') + '"]').text('00:00').addClass('hidden');
  });

  recorder && recorder.clear();
  recorder && clearTimeout(wo_timeout);
  Wo_clearPRecording();
  Wo_clearMRecording();
}

function Wo_ClearTimeout() {
  clearTimeout(wo_timeout);
}
function Wo_ShowRecordingTime(self) {
  var time = self.text();
  var seconds = time.split(":");
  var date = new Date();
  date.setHours(0);
  date.setMinutes(seconds[0]);
  date.setSeconds(seconds[1]);
  var __date = new Date(date.valueOf() + 1000);
  var temp = __date.toTimeString().split(" ");
  var timeST = temp[0].split(":");
  if (timeST[1] >= 10) {
    Wo_ClearTimeout();
    Wo_stopRecording();
  }
  else {
    self.text(timeST[1] + ":" + timeST[2]);
    wo_timeout = setTimeout(Wo_ShowRecordingTime, 1000, recording_time)
  }

}
var audio_context, recorder, recording_time, wo_timeout, localstream, recording_node, chat_tab, comm_field;
function Wo_startUserMedia(stream) {
  localstream = stream;
  var input = audio_context.createMediaStreamSource(stream);
  if (input) {
    recorder = new Recorder(input, { bufferLen: 16384 });
  }
  else {
    console.log('Could not initialize media stream');
  }
}

function Wo_startRecording() {
  recorder && recorder.record();
  recording_time.removeClass('hidden');
  recorder && recorder.exportWAV(function (blob) { });
  recorder && setTimeout(Wo_ShowRecordingTime, 1000, recording_time);
  //console.log('recording started');
}

function Wo_stopRecording() {
  recorder && recorder.stop();
  wo_timeout && clearTimeout(wo_timeout);
  //recorder     && console.log('recording sotopped');
}

function Wo_StopLocalStream() {
  localstream && localstream.getTracks().forEach(function (track) { track.stop() });
  localstream = false;
  recording_node = false;
  delete (recorder);
}

function Wo_clearPRecording() {
  recorder && recorder.clear();
  recording_time && recording_time.text('00:00');
  recorder && clearTimeout(wo_timeout);
  recording_time && recording_time.addClass('hidden');
  $("#recordPostAudio").html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mic"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>').attr('data-record', '0');
}

function Wo_clearMRecording() {
  recorder && recorder.clear();
  recording_time && recording_time.text('00:00');
  recorder && clearTimeout(wo_timeout);
  recording_time && recording_time.addClass('hidden');
  $("#messages-record").html('<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24"><path fill="#ff3a55" d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z"></path></svg>').attr('data-record', '0');
}

function Wo_GetPRecordLink() {
  var publisher_button = $('#publisher-button');
  publisher_button.attr('disabled', true);
  publisher_button.addClass('btn-loading');
  if (recorder && recording_node == "post") {
    recorder.exportWAV(function (blob) {
      if (blob instanceof Blob && blob.size > 50) {
        var fileName = (new Date).toISOString().replace(/:|\./g, '-');
        var file = new File([blob], 'wo-' + fileName + '.wav', { type: 'audio/wav' });
        var dataForm = new FormData();
        dataForm.append('audio-filename', file.name);
        dataForm.append('audio-blob', file);
        Wo_RegisterPost(dataForm);
      }
      else { $('form.post').submit() }
    });
  }
  else { $('form.post').submit() }
}

function Wo_GetMRecordLink() {
  if (recorder && recording_node == "msg") {
    recorder.exportWAV(function (blob) {
      if (blob instanceof Blob && blob.size > 50) {
        var fileName = (new Date).toISOString().replace(/:|\./g, '-');
        var file = new File([blob], 'AU-' + fileName + '.wav', { type: 'audio/wav' });
        var dataForm = new FormData();
        dataForm.append('audio-filename', file.name);
        dataForm.append('audio-blob', file);
        Wo_RegisterMessage(dataForm);
      }
      else { $('form.sendMessages').submit() }

    });
  }
  else { $('form.sendMessages').submit() }
}

function Wo_RegisterTabMessage(id, type = '') {

  if (!id) {
    return false;
  }

  if (type == 'page') {
    chat_tab = id;
  }

  if (recorder && recording_node == "chat" && id == chat_tab) {
    recorder.exportWAV(function (blob) {
      if (blob instanceof Blob && blob.size > 50) {
        var fileName = (new Date).toISOString().replace(/:|\./g, '-');
        var file = new File([blob], 'AU-' + fileName + '.wav', { type: 'audio/wav' });
        var dataForm = new FormData();
        dataForm.append('audio-filename', file.name);
        dataForm.append('audio-blob', file);
        Wo_RegisterTabMessageRecord(dataForm, id, type);
      }
      else {
        if (type == 'page') {
          $('form.page-chat-sending-' + id).submit();
          $('[name=chatSticker]').val('');
        }
        else {
          $('form.chat-sending-form-' + id).submit();
          $('[name=chatSticker]').val('');
        }
      }

    });
  }
  else {
    if (type == 'page') {
      $('form.page-chat-sending-' + id).submit();
      $('[name=chatSticker]').val('');
    }
    else {
      $('form.chat-sending-form-' + id).submit();
      $('[name=chatSticker]').val('');
    }
  }
}

function Wo_RegisterTabMessageRecord(dataForm, id, type = '') {
  if (dataForm && id) {
    var form_class = 'chat-sending-form-';
    if (type == 'page') {
      form_class = 'page-chat-sending-';
    }
    $('form.' + form_class + id).find('.ball-pulse').fadeIn(100);
    $.ajax({
      url: Wo_Ajax_Requests_File() + "?f=chat&s=register_message_record",
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: dataForm,
      processData: false,
      contentType: false,
      xhr: function () {
        var xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function (evt) {
          if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
          }
        }, false);
        return xhr;
      }
    }).done(function (data) {
      if (data.status == 200) {
        $('form.' + form_class + id).find('input.message-record').val('');
        $('form.' + form_class + id).find('input.media-name').val('');
        $('form.' + form_class + id).find('.ball-pulse').fadeOut(100);;
        Wo_stopRecording();
        var color = '';
        if ($('form.' + form_class + id).find('#color').length > 0) {
          color = $('form.' + form_class + id).find('#color').val();
        }
        Wo_CleanRecordNodes(color);
        Wo_StopLocalStream();
        $('form.' + form_class + id).find('input.message-record').val(data.url);
        $('form.' + form_class + id).find('input.media-name').val(data.name);
        var color = $('.chat-sending-form-' + id + ' #color').val();
        if (node_socket_flow === "1") {
          socket.emit("private_message", {
            to_id: id,
            from_id: _getCookie("user_id"),
            msg: "",
            color: color,
            mediaFilename: data.url,
            mediaName: data.name,
            record: true
          })
        }
        else {
          $('form.' + form_class + id).submit();
        }
        console.log("Done")
      }
    });
  }
}

function Wo_RegisterPost(dataForm) {
  if (dataForm) {
    $.ajax({
      url: Wo_Ajax_Requests_File() + "?f=posts&s=register_post_record",
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: dataForm,
      processData: false,
      contentType: false,
    }).done(function (data) {
      if (data.status == 200) {
        Wo_stopRecording();
        Wo_clearPRecording();
        Wo_StopLocalStream();
        $("#postRecord").val(data.url)
        $('form.post').submit()
      }
    });
  }
}

function Wo_RegisterMessage(dataForm) {
  if (dataForm) {
    $.ajax({
      url: Wo_Ajax_Requests_File() + "?f=messages&s=upload_record",
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: dataForm,
      processData: false,
      contentType: false,
    }).done(function (data) {
      if (data.status == 200) {
        Wo_stopRecording();
        Wo_clearMRecording();
        Wo_StopLocalStream();
        $("#message-record-file").val(data.url);
        $("#message-record-name").val(data.name);
        if (node_socket_flow === "1") {
          socket.emit("private_message_page", {
            to_id: $("#users-message.active").find(".messages-recipients-list").attr("id").substr("messages-recipient-".length),
            from_id: _getCookie("user_id"),
            msg: "",
            color: $(".send-button").css("background-color"),
            mediaFilename: data.url,
            mediaName: data.name,
            record: true,
            isSticker: false
          })
        } else {
          $('form.sendMessages').submit();
        }
        console.log("Done")
      }
    });
  }
}

function Wo_RegisterComment(text, post_id, user_id, event, page_id, type,gif_url = '') {
  $('.chat-box-stickers-cont').html('');
  $('#gif-form-'+post_id).slideUp(200);
  if (!text) {
    text = $('[id=post-' + post_id + ']').find('.comment-textarea').val();
  }


  if (event.keyCode == 13 && event.shiftKey == 0 && recording_node == "comm") {
    Wo_stopRecording();
    if (recorder) {
      recorder.exportWAV(function (blob) {
        var comment_src_image = $('#post-' + post_id).find('#comment_src_image');
        var comment_image = '';
        if (comment_src_image.length > 0) {
          comment_image = comment_src_image.val();
        }
        var dataForm = new FormData();
        dataForm.append('post_id', post_id);
        dataForm.append('text', text);
        dataForm.append('user_id', user_id);
        dataForm.append('page_id', page_id);
        dataForm.append('comment_image', comment_image);
        dataForm.append('gif_url', gif_url);
        if (blob.size > 50) {
          var fileName = (new Date).toISOString().replace(/:|\./g, '-');
          var file = new File([blob], 'wo-' + fileName + '.wav', { type: 'audio/wav' });
          dataForm.append('audio-filename', file.name);
          dataForm.append('audio-blob', file);
        }
        Wo_InsertComment(dataForm, post_id);
      });
    }

    else {
      var comment_src_image = $('#post-' + post_id).find('#comment_src_image');
      var comment_image = '';
      if (comment_src_image.length > 0) {
        comment_image = comment_src_image.val();
      }
      var dataForm = new FormData();
      dataForm.append('post_id', post_id);
      dataForm.append('text', text);
      dataForm.append('user_id', user_id);
      dataForm.append('page_id', page_id);
      dataForm.append('comment_image', comment_image);
      dataForm.append('gif_url', gif_url);
      $('#charsLeft_' + post_id).text($('#charsLeft_' + post_id).attr('data_num'));
      Wo_InsertComment(dataForm, post_id);
    }
  }
}

function Wo_RegisterComment2(post_id, user_id, page_id, type,gif_url = '') {
  $('.chat-box-stickers-cont').html('');
  $('#gif-form-'+post_id).slideUp(200);
  text = $('[id=post-' + post_id + ']').find('.comment-textarea').val();
  //if (recording_node == "comm") {
    Wo_stopRecording();
    if (recorder) {
      recorder.exportWAV(function (blob) {
        var comment_src_image = $('#post-' + post_id).find('#comment_src_image');
        var comment_image = '';
        if (comment_src_image.length > 0) {
          comment_image = comment_src_image.val();
        }
        var dataForm = new FormData();
        dataForm.append('post_id', post_id);
        dataForm.append('text', text);
        dataForm.append('user_id', user_id);
        dataForm.append('page_id', page_id);
        dataForm.append('comment_image', comment_image);
        dataForm.append('gif_url', gif_url);
        if (blob.size > 50) {
          var fileName = (new Date).toISOString().replace(/:|\./g, '-');
          var file = new File([blob], 'wo-' + fileName + '.wav', { type: 'audio/wav' });
          dataForm.append('audio-filename', file.name);
          dataForm.append('audio-blob', file);
        }
        Wo_InsertComment(dataForm, post_id);
      });
    }

    else {
      var comment_src_image = $('#post-' + post_id).find('#comment_src_image');
      var comment_image = '';
      if (comment_src_image.length > 0) {
        comment_image = comment_src_image.val();
      }
      var dataForm = new FormData();
      dataForm.append('post_id', post_id);
      dataForm.append('text', text);
      dataForm.append('user_id', user_id);
      dataForm.append('page_id', page_id);
      dataForm.append('comment_image', comment_image);
      dataForm.append('gif_url', gif_url);
      $('#charsLeft_' + post_id).text($('#charsLeft_' + post_id).attr('data_num'));
      Wo_InsertComment(dataForm, post_id);
    }
  //}
}

function Wo_InsertComment(dataForm, post_id) {
  if (!dataForm) { return false; }
  post_wrapper = $('[id=post-' + post_id + ']');
  comment_textarea = post_wrapper.find('.post-comments');
  comment_btn = comment_textarea.find('.emo-comment');
  textarea_wrapper = comment_textarea.find('.textarea');
  comment_list = post_wrapper.find('.comments-list');
  //event.preventDefault();
  textarea_wrapper.val('');
  
  post_wrapper.find('#wo_comment_combo .ball-pulse').fadeIn(100);
  $.ajax({
    url: Wo_Ajax_Requests_File() + '?f=posts&s=register_comment&hash=' + $('.main_session').val(),
    type: 'POST',
    cache: false,
    dataType: 'json',
    data: dataForm,
    processData: false,
    contentType: false,
  }).done(function (data) {
    $('.wo_comment_combo_' + post_id).removeClass('comment-toggle');
    if (data.status == 200) {
      if (node_socket_flow == "1") {
        socket.emit("post_notification", { post_id: post_id, user_id: _getCookie("user_id"), type: "added" });
      }
      Wo_CleanRecordNodes();
      post_wrapper.find('.post-footer .comment-container:last-child').after(data.html);
      post_wrapper.find('.comments-list-lightbox .comment-container:first').before(data.html);
      post_wrapper.find('[id=comments]').html(data.comments_num);
      post_wrapper.find('.lightbox-no-comments').remove();
      Wo_StopLocalStream();
      if (data.mention.length > 0 && node_socket_flow == "1") {
        $.each(data.mention, function( index, value ) {
          socket.emit("user_notification", { to_id: value, user_id: _getCookie("user_id")});
        });
      }
    }
    $('#post-' + post_id).find('.comment-image-con').empty().addClass('hidden');
    $('#post-' + post_id).find('#comment_src_image').val('');
    post_wrapper.find('#wo_comment_combo .ball-pulse').fadeOut(100);
    if (data.can_send == 1) {
      Wo_SendMessages();
    }
  });
}