@use('Sweet1s\MoonshineFileManager\FileManagerTypeEnum')

@php($uniqueID = $element->id() . '_' . Str::random(5))

<div x-data="fileManager">
    <x-moonshine::form.input
        x-ref="fileManagerInput"
        x-on:change="eventChangeFMInput"
        type="hidden"
        id="lfm-input__{{ $uniqueID }}"
        class="lfm-input__{{ $uniqueID }}"
        :name="$element->name()"
        :value="implode(',', $element->getFullPathValues())"
    />

    <div class="form-group form-group-dropzone">
        <x-moonshine::form.button
            x-ref="fileManagerButton"
            data-input="lfm-input__{{ $uniqueID  }}"
            class="lfm__{{ $uniqueID }}"
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
                            :itemAttributes="value($element->resolveItemAttributes(), $file, $index)"
                        />
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</div>

<script>
    let lfm_{{ $uniqueID }} = function (id, options) {
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

    let route_prefix_{{ $uniqueID }} = "{{ url('filemanager') }}";
    lfm_{{ $uniqueID }}('lfm__{{ $uniqueID }}', {prefix: route_prefix_{{ $uniqueID }}});

    let lfmInput{{ $uniqueID }} = document.querySelector('.lfm-input__{{ $uniqueID }}');

    document.querySelectorAll('.lfm__{{ $uniqueID }} + * button').forEach((button) => {

        button.addEventListener('click', function () {
            let image = button.nextElementSibling.getAttribute('src');

            let values = lfmInput{{ $uniqueID }}.value.split(',');

            if (values.includes(image)) {
                values = values.filter(function (value) {
                    return value !== image;
                });
            } else {
                values.push(image);
            }

            lfmInput{{ $uniqueID }}.value = values.join(',');
        });
    });

    document.addEventListener('alpine:init', () => {
        Alpine.data('fileManager', () => ({
            eventChangeFMInput() {
                let values = this.$refs.fileManagerInput.value.split(',');

                let newValues = new Set(values);

                this.$refs.fileManagerButton.innerText = `{{ $element->getTitle() }} (${newValues.size})`
            }
        }))
    });
</script>
