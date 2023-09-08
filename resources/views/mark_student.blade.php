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
                            <h3 class="font-semibold text-base text-blueGray-700">Attendance: {{ $student['firstname'].' '.$student['middlename'].' '.$student['lastname'] }} ({{ $student['matno'] }})</h3>
                            <input id="id" type="hidden" name="id" value="{{ $student['id'] }}">
                            <input id="csrf" type="hidden" value="{{ csrf_token() }}" />
                        </div>
                        <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
{{--                            <h1>Face Identification</h1>--}}
                            <button class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150" id="identifyButton">Identify</button>
                            <div id="results"></div>
{{--                            <video id="video" width="640" height="480" autoplay></video>--}}
{{--                            <canvas id="canvas" width="640" height="480"></canvas>--}}


                        </div>
                    </div>
                </div>

                <div class="block w-full overflow-x-auto">
                    <h1>Face Identification</h1>
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
            const identifyButton = $('#identifyButton');
            const resultsDiv = $('#results');

            await faceapi.nets.tinyFaceDetector.loadFromUri("{{ asset('/models/tiny_face_detector') }}");
            await faceapi.nets.faceLandmark68Net.loadFromUri("{{ asset('/models/face_landmark_68') }}");
            await faceapi.nets.faceRecognitionNet.loadFromUri("{{ asset('/models/face_recognition') }}");

            const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
            video.srcObject = stream;

            identifyButton.click(async function() {
                identifyButton.hide();
                resultsDiv.html('<p>Scanning Face...</p>');
                const detections = await faceapi.detectSingleFace(video,
                    new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();

                if (detections) {
                    const queryDescriptor = detections.descriptor;

                    resultsDiv.html('<p>Identifying...</p>');

                    const response = {!! $student->facedata !!}

                    const distance = faceapi.euclideanDistance(queryDescriptor, response);
                    if (distance < 0.6) {
                        resultsDiv.html('<p>Identified user.</p>');
                    } else {
                        resultsDiv.html('<p>Unknown user.</p>');
                    }
                } else {
                    resultsDiv.html('<p>No face detected. Please try again.</p>');
                }
            });
        });
    </script>

</x-app-layout>
