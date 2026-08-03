
<div class="ibox">
    <div class="ibox-title">
        <div>
            <h5>Tạo chương trình bài học</h5>
        </div>
        <div class="description">Cho phép bạn bán các các chương trình chi tiết của một bài học</div>
    </div>
    <div class="ibox-content">
        <div class="variant-foot mt10">
            <button type="button" class="add-program">Thêm chương trình mới</button>
        </div>

        <div class="program-content mt20">
            @php
                $oldChapters = old('chapter', $product->chapter ?? [])
            @endphp

            @if(isset($oldChapters) && is_array($oldChapters) && count($oldChapters))
            {{-- @dd($oldChapters) --}}
            @foreach($oldChapters as $chapterIndex => $chapter)
                <div class="ibox mt20 chapter-wrapper" data-chapter-index="{{ $chapterIndex }}">
                    <div class="ibox-title">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <input type="text" 
                                name="chapter[{{ $chapterIndex }}][title]" 
                                class="form-control" 
                                value="{{ $chapter['title'] ?? '' }}" 
                                placeholder="Nhập vào tên Chapter" style="width:75%;">
                            <div class="chapter-action">
                                <button type="button" class="add-chapter-item mr10">+Thêm bài học</button>
                                <button type="button" class="remove-chapter-item">Xóa chương</button>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        @if(!empty($chapter['content']))
                            @foreach($chapter['content'] as $lessonIndex => $lesson)
                                <div class="chapter-item" data-lesssion-index="{{ $lessonIndex }}">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="chapter-content">
                                                <div class="title mb10">
                                                    <input type="text" class="form-control"
                                                        name="chapter[{{ $chapterIndex }}][content][{{ $lessonIndex }}][title]"
                                                        value="{{ $lesson['title'] ?? '' }}"
                                                        placeholder="Nhập vào tên bài học">
                                                </div>
                                                <div class="description">
                                                    <input type="text" class="form-control"
                                                        name="chapter[{{ $chapterIndex }}][content][{{ $lessonIndex }}][description]"
                                                        value="{{ $lesson['description'] ?? '' }}"
                                                        placeholder="Nhập vào mô tả bài học">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="chapter-info">
                                                <div class="chapter-time mb10">
                                                    <input type="text" class="form-control"
                                                        name="chapter[{{ $chapterIndex }}][content][{{ $lessonIndex }}][time]"
                                                        value="{{ $lesson['time'] ?? '' }}"
                                                        placeholder="Thời lượng">
                                                </div>
                                                <div class="chapter-type">
                                                    <input type="text" class="form-control"
                                                        name="chapter[{{ $chapterIndex }}][content][{{ $lessonIndex }}][type]"
                                                        value="{{ $lesson['type'] ?? '' }}"
                                                        placeholder="Loại bài học">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-1">
                                            <button type="button" class="form-control btn btn-danger remove-chapter">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
            @endif
        </div>
    </div>
</div>

