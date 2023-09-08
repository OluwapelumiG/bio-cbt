<x-app-layout>
    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100">

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <form method="POST" id="matfrm">
                <input id="csrf" type="hidden" value="{{ csrf_token() }}" />

                <!-- Email Address -->
                <div>
                    <x-input-label for="matno" :value="__('Matric No')" />
                    <x-text-input id="matno" class="block mt-1 w-full" type="text" name="matno" :value="old('matno')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('matno')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <!-- <x-primary-button class="ml-3">
                        {{ __('Submit') }}
                    </x-primary-button> -->
                    <button id="matbtn" class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button">Submit</button>
                    <div id="mloader" class="hidden animate-spin inline-block w-10 h-10 border-[3px] border-current border-t-transparent text-green-600 rounded-full" role="status" aria-label="loading">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script src="{{ asset('/face/dist/face-api.js') }}"></script>


    <script>
        $(document).ready(async function() {
            const matButton = $('button#matbtn');
            const matFrm = $('form#matfrm');


            matFrm.submit(async function(event) {
                event.preventDefault();
                $(matButton).hide();
                $("#mloader").show();
                let matno = $("#matno").val();
                let csrf = $("#csrf").val();
                if (!matno){ alert('Matno cannot be null'); return 1; }

                const obj = {
                    matno: matno,
                    _token: csrf,
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('students.find_matno') }}",
                    data: obj,

                    success: function(result) {
                        if(result.message === 'success'){
                            window.location.replace(result.redirect);
                            $("#mloader").hide();
                            $(matButton).show();
                        }
                        else{
                            alert("Invalid matno");
                            $(matButton).show();
                            $("#mloader").hide();
                        }
                    },
                    error: function() {
                        $(matButton).hide();
                        $("#mloader").show();
                        console.error('error')
                    }
                });
            });
        });
    </script>

</x-app-layout>
