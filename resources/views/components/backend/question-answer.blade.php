<div class="ibox ibox-ques">
    <div class="ibox-title">
        <div>
            <h5>Hỏi & Đáp</h5>
        </div>
    </div>
    <div class="ibox-content">
        <div class="variant-foot mt10">
            <button type="button" class="add-question">Tạo câu hỏi</button>
        </div>
        <div class="program-content mt20">
            @php
                $ques_answers = old('question_answer', !is_null($model) ? $model->question_answer : []);
            @endphp
            @if(isset($ques_answers) && is_array($ques_answers) && count($ques_answers))
                @foreach($ques_answers as $index => $ques_answer)
                    <div class="ibox mt20 question-wrapper" data-question-index="{{ $index }}">
                        <div class="ibox-title">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                                <input type="text" 
                                    name="question_answer[{{ $index }}][question]" 
                                    class="form-control" 
                                    value="{{ $ques_answer['question'] ?? '' }}" 
                                    placeholder="Nhập câu hỏi" style="width:80%;">
                                <div class="chapter-action">
                                    <button type="button" class="remove-question-item">Xóa câu hỏi</button>
                                </div>
                            </div>
                            <div class="form-row">
                                <label for="" class="control-label text-left">Câu trả lời</label>
                                <textarea name="question_answer[{{ $index }}][answer]" class="ck-editor" id="question_answer[{{ $index }}]" placeholder="{{ __('messages.enter_desc_name') }}" style="width:100%; margin-top:10px;">{{ $ques_answer['answer'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>