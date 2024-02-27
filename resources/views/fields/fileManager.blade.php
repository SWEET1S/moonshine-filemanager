@use('Sweet1s\MoonshineFileManager\FileManagerTypeEnum')

<x-moonshine::form.input
    type="hidden"
    id="lfm-input"
    class="lfm-input"
    :name="$element->name()"
    :value="implode(',', $element->getFullPathValues())"
/>

<div class="form-group form-group-dropzone">
    <x-moonshine::form.button
        data-input="lfm-input"
        class="lfm"
    >
        {{ $element->getTitle() }}
    </x-moonshine::form.button>

    @php($raw = is_iterable($value) ? $value : [$value])
    @php($files = $element->getFullPathValues())

    @if(is_array($files) ? array_filter($files) : $files->isNotEmpty())
        <div class="dropzone">
            <div class="dropzone-items"
                 x-data="sortable"
                 data-handle=".dropzone-item"
            >
                @foreach($files as $index => $file)
                    <x-moonshine::form.file-item
                        :attributes="$element->attributes()->merge([
                            'id' => $element->id(),
                            'name' => $element->name()
                        ])"
                        :raw="$raw[$index]"
                        :file="$file"
                        :download="$element->getTypeOfFileManager() === FileManagerTypeEnum::Image ? false : $element->canDownload()"
                        :removable="$element->isRemovable()"
                        :removableAttributes="$element->getRemovableAttributes()"
                        :imageable="$element->getTypeOfFileManager() === FileManagerTypeEnum::Image"
                    />
                @endforeach
            </div>
        </div>
    @endif
</div>


<script>
    let lfm = function (id, options) {
        let button = document.querySelector(`.${id}`);

        button.addEventListener('click', function () {
            let route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
            let target_input = document.getElementById(button.getAttribute('data-input'));

            window.open(route_prefix + '?type=' + '{{ $element->getTypeOfFileManager()->value }}', 'FileManager', 'width=900,height=600');
            window.SetUrl = function (items) {
                let file_path = items.map(function (item) {
                    return item.url;
                }).join(',');

                // set the value of the desired input to image url
                target_input.value = file_path;
                target_input.dispatchEvent(new Event('change'));

                // set or change the preview image src
                items.forEach(function (item) {
                    let img = document.createElement('img')
                    img.setAttribute('style', 'height: 5rem')
                    img.setAttribute('src', item.thumb_url)
                });
            };
        });
    };

    let route_prefix = "{{ url('filemanager') }}";
    lfm('lfm', {prefix: route_prefix});

    let lfmInput = document.querySelector('.lfm-input');

    let initialValues = JSON.parse('{!! json_encode($element->getFullPathValues()) !!}');

    document.querySelectorAll('.lfm + * button').forEach((button) => {
        button.addEventListener('click', function () {
            let image = button.nextElementSibling.getAttribute('src');

            let values = lfmInput.value.split(',');

            if (values.includes(image)) {
                values = values.filter(function (value) {
                    return value !== image;
                });
            } else {
                values.push(image);
            }

            lfmInput.value = values.join(',');
        });
    });

    lfmInput.addEventListener('change', function () {
        let values = lfmInput.value.split(',');

        let newValues = new Set(initialValues.concat(values));

        lfmInput.value = Array.from(newValues).join(',') || null;

        document.querySelector('.lfm').innerText = `{{ $element->getTitle() }} (${newValues.size})`
    });
</script>
