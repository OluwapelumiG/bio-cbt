<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="rounded-t mb-0 px-4 py-3 border-0">
                    <div class="flex flex-wrap items-center">
                        <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                            <h3 class="font-semibold text-base text-blueGray-700">Enroll: {{ $student['firstname'].' '.$student['middlename'].' '.$student['lastname'] }} ({{ $student['matno'] }})</h3>
                            <input id="id" type="hidden" name="id" value="{{ $student['id'] }}">
                            <input id="csrf" type="hidden" value="{{ csrf_token() }}" />
                        </div>
                        <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                            <button id="captureButton" class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button">Capture Face</button>
                            <button id="registerButton" class="hidden bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" type="button">Register Face</button>
                            <div id="loader" class="hidden animate-spin inline-block w-10 h-10 border-[3px] border-current border-t-transparent text-yellow-1000 rounded-full" role="status" aria-label="loading">
                                <span class="sr-only">Loading...</span>
                            </div>

                            <div id="sloader" class="hidden animate-spin inline-block w-10 h-10 border-[3px] border-current border-t-transparent text-green-600 rounded-full" role="status" aria-label="loading">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div id="results"></div>
                        </div>
                    </div>
                </div>

                <div class="block w-full overflow-x-auto">
                    <h1>Face Registration</h1>
                    <video id="video" width="640" height="480" autoplay></video>
{{--                    <canvas id="canvas" width="640" height="480"></canvas>--}}

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('/face/dist/face-api.js') }}"></script>
    <script>
// import * as faceapi from 'face-api.js';

        $(document).ready(async function() {
            const video = $('#video')[0];
            // const canvas = $('#canvas')[0];
            const captureButton = $('#captureButton');
            const registerButton = $('#registerButton');
            const resultsDiv = $('#results');
            let capturedDescriptor = null;

            await faceapi.nets.tinyFaceDetector.loadFromUri("{{ asset('/models/tiny_face_detector') }}");
            await faceapi.nets.faceLandmark68Net.loadFromUri("{{ asset('/models/face_landmark_68') }}");
            await faceapi.nets.faceRecognitionNet.loadFromUri("{{ asset('/models/face_recognition') }}");

            const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
            video.srcObject = stream;

            captureButton.click(async function() {
                $("#captureButton").hide();
                $("#loader").show();
                const detections = await faceapi.detectSingleFace(video,
                    new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();

                if (detections) {
                    capturedDescriptor = detections.descriptor;
                    $("#loader").hide();
                    $("#registerButton").show();
                    video.pause();
                    resultsDiv.html('<p>Face captured. Ready to register.</p>');
                } else {
                    $("#loader").hide();
                    $("#captureButton").show();
                    resultsDiv.html('<p>No face detected. Please try again.</p>');
                }
            });

            registerButton.click(function() {
                if (capturedDescriptor) {
                    let id = $("#id").val();
                    let csrf = $("#csrf").val();
                    if (!id) {return "Hello"; }
                    const obj = {
                        id: id,
                        _token: csrf,
                        facedata: capturedDescriptor
                    };
                    $.ajax({
                        type: "POST",
                        url: "{{ route('students.save_face') }}",
                        data: obj,

                        success: function(result) {
                            console.log(result);
                        },
                        error: function() {
                            console.error('error')
                        }
                    });
                    // Here, you can send 'capturedDescriptor' to your server for registration.
                    // Example: $.post('/register-face', { descriptor: capturedDescriptor });
                    resultsDiv.html('<p>Face registered!</p>');
                    capturedDescriptor = null; // Reset captured descriptor
                } else {
                    $("#loader").hide();
                    $("#registerButton").hide();
                    $("#captureButton").show();
                    resultsDiv.html('<p>No face captured. Please capture a face first.</p>');
                }
            });
        });
    </script>

</x-app-layout>
