@use('Illuminate\Support\Str')
@use('Illuminate\Support\Collection')
@use ('Sweet1s\MoonshineFileManager\FileManagerTypeEnum')

@php

    /** @var ?Collection $value */

@endphp

@php($uniqueID = Str::random(5))

<div x-data="fileManager">
    <x-moonshine::form.input
        x-ref="fileManagerInput"
        x-on:change="eventChangeFMInput"
        type="hidden"
        id="lfm-input__{{ $uniqueID }}"
        class="lfm-input__{{ $uniqueID }}"
        name="{{ $column }}"
    />

    <div class="form-group form-group-dropzone">
        <x-moonshine::form.button
            x-ref="fileManagerButton"
            data-input="lfm-input__{{ $uniqueID  }}"
            class="lfm__{{ $uniqueID }}"
        >
            {{ $title }}
        </x-moonshine::form.button>

        <div class="dropzone">
            <div class="dropzone-items"
                 x-data="sortable"
                 data-handle=".dropzone-item"
            >
                @foreach($files as $index => $file)
                    <x-moonshine::form.file-item
                        :attributes="$attributes"
                        :itemAttributes="$file['attributes']"
                        :filename="$file['name']"
                        :raw="$file['raw_value']"
                        :file="$file['full_path']"
                        :removable="$isRemovable"
                        :removableAttributes="$removableAttributes"
                        :hiddenAttributes="$hiddenAttributes"
                        :imageable="$typeOfFileManager === FileManagerTypeEnum::Image"
                    />
                @endforeach
            </div>
        </div>
    </div>

</div>

<script>
    let lfm_{{ $uniqueID }} = function (id, options) {
        let button = document.querySelector(`.${id}`);

        button.addEventListener('click', function () {
            let route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
            let target_input = document.getElementById(button.getAttribute('data-input'));

            window.open(route_prefix + '?type=' + '{{ $typeOfFileManager->value }}', 'FileManager', 'width=900,height=600');
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

    const getPath = (el) => {
        if (el.nextElementSibling && el.nextElementSibling.hasAttribute('src')) {
            return el.nextElementSibling.getAttribute('src')
        } else if (el.previousElementSibling && el.previousElementSibling.querySelector('a')) {
            return el.previousElementSibling.querySelector('a').getAttribute('href')
        }
        return null
    }
    document.querySelectorAll('lfm__{{ $uniqueID }}').forEach((button) => {

        button.addEventListener('click', function () {
            let path = getPath(button);

            let values = lfmInput{{ $uniqueID }}.value.split(',');

            if (values.includes(path)) {
                values = values.filter(function (value) {
                    return value !== path;
                });
            } else {
                values.push(path);
            }
            lfmInput{{ $uniqueID }}.value = values.join(',');
        });
    });

    document.addEventListener('alpine:init', () => {
        Alpine.data('fileManager', () => ({
            eventChangeFMInput() {
                let values = this.$refs.fileManagerInput.value.split(',');

                let newValues = new Set(values);

                this.$refs.fileManagerButton.innerText = `{{ $title }} (${newValues.size})`
            }
        }))
    });
</script>
