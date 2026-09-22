<template>
    <Card class="flex flex-col">
        <!-- 초기 화면: 참가 버튼만 표시 -->
        <div v-if="!isFullscreen" class="p-6">
            <h1 class="text-xl font-light text-gray-500 mb-6">
                Online Meeting
            </h1>
            <div class="text-center py-12">
                <button
                    @click="joinMeeting"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-lg font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                    <svg
                        class="w-6 h-6 mr-3"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                        />
                    </svg>
                    Join Meeting
                </button>
            </div>
        </div>

        <!-- 전체화면 모달: 화상 화면과 채팅 -->
        <div v-if="isFullscreen" class="fixed inset-0 z-50 bg-gray-900">
            <div class="h-full flex">
                <!-- 메인 컨텐츠 영역 -->
                <div
                    class="flex-1 p-6 min-h-0 flex flex-col"
                    :class="{ 'pr-4': showChat }"
                >
                    <div
                        class="flex justify-between items-center mb-4 shrink-0"
                    >
                        <h1 class="text-xl font-light text-white">
                            Online Meeting
                        </h1>
                        <button
                            @click="leaveMeeting"
                            class="text-white hover:text-gray-300 focus:outline-none"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 min-h-0 flex flex-col gap-4">
                        <!-- 비디오 플레이어 영역 -->
                        <div class="flex-1 min-h-0 overflow-hidden">
                            <div
                                class="h-full flex flex-col space-y-3 items-stretch"
                            >
                                <div
                                    class="relative w-full remote-player-frame"
                                >
                                    <div
                                        id="remote-player"
                                        class="w-full bg-gray-800 rounded-lg overflow-hidden"
                                    ></div>
                                    <div
                                        class="absolute bottom-2 left-2 flex items-center space-x-2 bg-black bg-opacity-50 rounded-lg px-2 py-1"
                                    >
                                        <div
                                            class="bg-black bg-opacity-50 text-white text-sm px-2 py-1 rounded"
                                        >
                                            Smart Glass Screen
                                        </div>
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <div
                                                :class="{
                                                    'w-2 h-2 rounded-full': true,
                                                    'bg-green-500':
                                                        isRemoteConnected,
                                                    'bg-gray-500':
                                                        !isRemoteConnected,
                                                }"
                                            ></div>
                                            <span class="text-white text-xs">{{
                                                isRemoteConnected
                                                    ? "Connected"
                                                    : "Disconnected"
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-center">
                                    <div
                                        class="relative w-full local-player-frame"
                                    >
                                        <div
                                            id="local-player"
                                            class="w-full bg-gray-800 rounded-lg overflow-hidden"
                                        ></div>
                                        <div
                                            class="absolute bottom-2 left-2 flex items-center space-x-2 bg-black bg-opacity-50 rounded-lg px-2 py-1"
                                        >
                                            <div
                                                class="bg-black bg-opacity-50 text-white text-sm px-2 py-1 rounded"
                                            >
                                                My Screen
                                            </div>
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <div
                                                    :class="{
                                                        'w-2 h-2 rounded-full': true,
                                                        'bg-green-500':
                                                            isLocalConnected,
                                                        'bg-gray-500':
                                                            !isLocalConnected,
                                                    }"
                                                ></div>
                                                <span
                                                    class="text-white text-xs"
                                                    >{{
                                                        isLocalConnected
                                                            ? "Connected"
                                                            : "Disconnected"
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <div
                                                    :class="{
                                                        'w-2 h-2 rounded-full': true,
                                                        'bg-green-500':
                                                            isCameraOn,
                                                        'bg-red-500':
                                                            !isCameraOn,
                                                    }"
                                                ></div>
                                                <span
                                                    class="text-white text-xs"
                                                    >{{
                                                        isCameraOn
                                                            ? "Camera On"
                                                            : "Camera Off"
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <div
                                                    :class="{
                                                        'w-2 h-2 rounded-full': true,
                                                        'bg-green-500':
                                                            isMicrophoneOn,
                                                        'bg-red-500':
                                                            !isMicrophoneOn,
                                                    }"
                                                ></div>
                                                <span
                                                    class="text-white text-xs"
                                                    >{{
                                                        isMicrophoneOn
                                                            ? "Microphone On"
                                                            : "Microphone Off"
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 컨트롤 버튼 영역 -->
                        <div
                            class="shrink-0 border-t border-gray-700/70 pt-4 flex flex-wrap gap-3 justify-center bg-gray-900"
                        >
                            <button
                                @click="leaveMeeting"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    />
                                </svg>
                                Leave Meeting
                            </button>

                            <button
                                @click="toggleCamera"
                                :disabled="!isLocalConnected"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-500 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        v-if="isCameraOn"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                    />
                                    <path
                                        v-else
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                                    />
                                </svg>
                                {{
                                    isCameraOn
                                        ? "Turn off Camera"
                                        : "Turn on Camera"
                                }}
                            </button>

                            <button
                                @click="toggleMicrophone"
                                :disabled="!isLocalConnected"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-500 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        v-if="isMicrophoneOn"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                    />
                                    <path
                                        v-else
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                                    />
                                </svg>
                                {{
                                    isMicrophoneOn
                                        ? "Turn off Microphone"
                                        : "Turn on Microphone"
                                }}
                            </button>

                            <button
                                @click="
                                    isRecording
                                        ? stopRecording()
                                        : startRecording()
                                "
                                :disabled="!isLocalConnected"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-500 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        v-if="!isRecording"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                    />
                                    <path
                                        v-else
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                    <path
                                        v-if="isRecording"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"
                                    />
                                </svg>
                                {{
                                    isRecording
                                        ? "Stop Recording"
                                        : "Start Recording"
                                }}
                            </button>

                            <!-- 이미지 첨부 버튼 -->
                            <button
                                @click="selectImage"
                                :disabled="!isLocalConnected"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-500 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                Attach Image
                            </button>
                            <input
                                ref="imageInput"
                                type="file"
                                accept="image/*"
                                @change="handleImageUpload"
                                class="hidden"
                            />

                            <!-- 채팅 토글 버튼 -->
                            <button
                                @click="toggleChat"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            >
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                    />
                                </svg>
                                {{ showChat ? "Hide Chat" : "Show Chat" }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 채팅 영역 -->
                <div
                    v-if="showChat"
                    class="w-96 h-full bg-gray-800 border-l border-gray-700 flex flex-col"
                >
                    <div class="p-4 border-b border-gray-700">
                        <h2 class="text-lg font-medium text-gray-100">Chat</h2>
                    </div>

                    <!-- 메시지 영역 -->
                    <div
                        ref="messagesContainer"
                        class="flex-1 overflow-y-auto p-4 space-y-3"
                    >
                        <div
                            v-if="messages.length === 0"
                            class="flex items-center justify-center h-full text-gray-400"
                        >
                            메시지가 없습니다
                        </div>
                        <div
                            v-for="message in messages"
                            :key="message.id"
                            class="p-3 bg-gray-700 rounded-lg"
                        >
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-xs text-gray-400">
                                    {{ formatToLocalTime(message.created_at) }}
                                </span>
                                <span class="text-sm font-medium text-blue-400">
                                    {{ message.user?.name || "Unknown" }}
                                </span>
                            </div>
                            <p class="text-gray-300">
                                {{ message.content }}
                            </p>
                        </div>
                    </div>

                    <!-- 메시지 입력 영역 -->
                    <div class="p-4 border-t border-gray-700">
                        <input
                            ref="messageInput"
                            v-model="newMessage"
                            @keyup.enter.prevent="sendMessage"
                            type="text"
                            class="w-full px-4 py-2 rounded-lg border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-700 text-gray-100"
                            placeholder="Enter your message"
                            :disabled="isSending"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- 이미지 팝업 모달 -->
        <div
            v-if="showImageModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75"
            style="z-index: 100"
        >
            <div
                class="bg-gray-800 rounded-lg shadow-2xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col"
            >
                <div class="p-6 border-b border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-100">
                            Captured Image
                        </h3>
                        <div class="flex items-center space-x-4">
                            <!-- 도구 선택 -->
                            <div class="flex space-x-2">
                                <button
                                    @click="setTool('pen')"
                                    :class="{
                                        'bg-blue-600 hover:bg-blue-700':
                                            currentTool === 'pen',
                                        'bg-gray-600 hover:bg-gray-700':
                                            currentTool !== 'pen',
                                    }"
                                    class="px-3 py-2 rounded-lg text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                    title="Pen"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click="setTool('circle')"
                                    :class="{
                                        'bg-blue-600 hover:bg-blue-700':
                                            currentTool === 'circle',
                                        'bg-gray-600 hover:bg-gray-700':
                                            currentTool !== 'circle',
                                    }"
                                    class="px-3 py-2 rounded-lg text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                    title="Circle"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="8"
                                            stroke-width="2"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click="setTool('rectangle')"
                                    :class="{
                                        'bg-blue-600 hover:bg-blue-700':
                                            currentTool === 'rectangle',
                                        'bg-gray-600 hover:bg-gray-700':
                                            currentTool !== 'rectangle',
                                    }"
                                    class="px-3 py-2 rounded-lg text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                    title="Rectangle"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <rect
                                            x="6"
                                            y="6"
                                            width="12"
                                            height="12"
                                            stroke-width="2"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click="undoLastShape"
                                    class="px-3 py-2 rounded-lg bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="saveDrawing"
                                    class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors flex items-center"
                                >
                                    <svg
                                        class="w-4 h-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                    Save
                                </button>
                                <button
                                    @click="closeImageModal"
                                    class="text-gray-400 hover:text-gray-200 focus:outline-none transition-colors"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1 p-6 overflow-auto bg-gray-900">
                    <div class="flex justify-center items-center h-full">
                        <canvas
                            ref="canvas"
                            @mousedown="startDrawing"
                            @mousemove="draw"
                            @mouseup="stopDrawing"
                            @mouseleave="stopDrawing"
                            class="border border-gray-600 rounded-lg bg-white shadow-lg"
                            style="
                                max-width: 100%;
                                max-height: 100%;
                                object-fit: contain;
                            "
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>

<script setup>
import AgoraRTC from "agora-rtc-sdk-ng";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { nextTick, onMounted, onUnmounted, ref } from "vue";

const props = defineProps({
    resourceId: String,
    card: Object,
});

// 이미지 팝업 관련 상태
const showImageModal = ref(false);
const currentImage = ref("");
const shownImageIds = ref(new Set()); // 열린 이미지 ID 추적

// 캡처된 이미지 URL
const imageModalSource = ref(null);
const ignoreNextCapturedImage = ref(false);

// 이미지 폴링 관련 상태
const lastImageCheck = ref(null);
const pollingInterval = ref(null);

// 캔버스 관련 상태
const canvas = ref(null);
const ctx = ref(null);
const isDrawing = ref(false);
const startX = ref(0);
const startY = ref(0);
const currentTool = ref("pen");
const shapes = ref([]);
const baseImage = ref(null);
const paths = ref([]); // 자유 그리기 경로 저장
const currentPath = ref([]); // 현재 그리는 경로
const imageInput = ref(null); // 이미지 입력 참조

// 전체화면 관련 상태
const isFullscreen = ref(false);

// 채팅 관련 상태
const showChat = ref(true);
const messages = ref([]);
const newMessage = ref("");
const messagesContainer = ref(null);
const messagePollingInterval = ref(null);
const isSending = ref(false);
const messageInput = ref(null);

const APP_ID = "bd931baf9a7141c2bae4d026b7441fdc"; // Agora App ID
let channelName = ""; // 채널 이름
let token = "";
let agoraUid = 0; // 랜덤 UID

let rtc = {
    localAudioTrack: null,
    localVideoTrack: null,
    client: null,
};

const isCameraOn = ref(false);
const isMicrophoneOn = ref(false);
const isRecording = ref(false);
const recordingResourceId = ref(null);
const recordingSid = ref(null);

// 연결 상태 추적
const isLocalConnected = ref(false);
const isRemoteConnected = ref(false);

const echoChannelName = ref(null);
const taskLogSendSubscribed = ref(false);
const pendingTaskLogSendUrls = ref([]);

onMounted(() => {
    console.log("컴포넌트가 마운트되었습니다.");
    // Agora 클라이언트 객체 생성
    rtc.client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });
    initConnect();

    // ESC 키 이벤트 리스너 추가
    document.addEventListener("keydown", handleKeyDown);
});

// 컴포넌트가 제거될 때 정리
onUnmounted(async () => {
    console.log("컴포넌트가 언마운트되기 시작합니다.");

    // 폴링 중지
    if (pollingInterval.value) {
        console.log("이미지 폴링을 중지합니다.");
        clearInterval(pollingInterval.value);
    }

    if (messagePollingInterval.value) {
        console.log("메시지 폴링을 중지합니다.");
        clearInterval(messagePollingInterval.value);
    }

    unsubscribeTaskLogSendChannel();

    // 키보드 이벤트 리스너 제거
    document.removeEventListener("keydown", handleKeyDown);

    // 연결된 상태면 채널 나가기
    if (isLocalConnected.value) {
        await leaveChannel();
    }

    // 녹화 중이면 중지
    if (isRecording.value) {
        await stopRecording();
    }

    console.log("컴포넌트 언마운트가 완료되었습니다.");
});

// 미팅 참가
const joinMeeting = async () => {
    isFullscreen.value = true;
    document.body.style.overflow = "hidden";

    // Agora 채널 참가
    await joinChannel();

    // 이미지 폴링 시작
    startImagePolling();

    // 채팅 메시지 로드 및 폴링 시작
    loadMessages();
    messagePollingInterval.value = setInterval(loadMessages, 5000);

    subscribeTaskLogSendChannel();
    checkNewTaskLogSendImage();
};

// 미팅 나가기
const leaveMeeting = async () => {
    if (isLocalConnected.value) {
        await leaveChannel();
    }

    // 폴링 중지
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
    }
    if (messagePollingInterval.value) {
        clearInterval(messagePollingInterval.value);
    }

    unsubscribeTaskLogSendChannel();

    // 태스크 상태 업데이트
    try {
        const taskIdentifier = props.card?.taskCode || props.resourceId;

        await Nova.request().post(
            `/nova-vendor/live-card/tasks/${taskIdentifier}/complete`,
        );
    } catch (error) {
        console.error("태스크 상태 업데이트 실패:", error);
    }

    isFullscreen.value = false;
    document.body.style.overflow = "";
};

const startImagePolling = () => {
    checkNewTaskLogSendImage();
    pollingInterval.value = setInterval(checkNewTaskLogSendImage, 5000);
};

const initEcho = () => {
    const config = props.card?.echo;
    if (window.Echo || !config?.key) {
        return;
    }

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: config.broadcaster || "reverb",
        key: config.key,
        wsHost: config.wsHost,
        wsPort: config.wsPort,
        wssPort: config.wssPort,
        forceTLS: config.forceTLS,
        enabledTransports: config.enabledTransports || ["ws", "wss"],
    });
};

const subscribeTaskLogSendChannel = () => {
    const taskCode = props.card?.taskCode;
    if (!taskCode) {
        console.warn("LiveCard: taskCode가 없습니다.");
        return;
    }

    initEcho();

    if (!window.Echo) {
        console.warn(
            "LiveCard: Echo가 없습니다. BROADCAST_DRIVER=reverb 및 reverb 서버를 확인하세요.",
        );
        return;
    }

    echoChannelName.value = `task.${taskCode}`;

    if (taskLogSendSubscribed.value) {
        return;
    }

    window.Echo.channel(echoChannelName.value).listen(
        "TaskLogSendCaptured",
        (e) => {
            if (ignoreNextCapturedImage.value || !e?.imageUrl) {
                return;
            }

            const sendKey = e.taskLogSendId
                ? `send-${e.taskLogSendId}`
                : e.imageUrl;

            if (shownImageIds.value.has(sendKey)) {
                return;
            }

            shownImageIds.value.add(sendKey);
            showTaskLogSendImage(e.imageUrl);
        },
    );

    taskLogSendSubscribed.value = true;
    console.log("LiveCard: Echo 구독", echoChannelName.value);
};

const unsubscribeTaskLogSendChannel = () => {
    if (window.Echo && echoChannelName.value) {
        window.Echo.leave(echoChannelName.value);
    }
    echoChannelName.value = null;
    taskLogSendSubscribed.value = false;
};

const normalizeImageUrl = (imageUrl) => {
    if (!imageUrl) {
        return "";
    }

    if (
        imageUrl.startsWith("http://") ||
        imageUrl.startsWith("https://") ||
        imageUrl.startsWith("data:")
    ) {
        return imageUrl;
    }

    if (imageUrl.startsWith("/storage/")) {
        return imageUrl;
    }

    return `/storage/${imageUrl}`;
};

const openImageOnCanvas = async (imageUrl) => {
    imageModalSource.value = "received";
    currentImage.value = imageUrl;
    showImageModal.value = true;

    await nextTick();
    const img = new Image();
    img.crossOrigin = "anonymous";
    img.onload = () => {
        const canvasElement = canvas.value;
        canvasElement.width = img.width;
        canvasElement.height = img.height;

        ctx.value = canvasElement.getContext("2d");
        ctx.value.drawImage(img, 0, 0);
        baseImage.value = img;
        shapes.value = [];
        currentPath.value = [];
    };
    img.src = imageUrl;
};

const showTaskLogSendImage = async (imageUrl) => {
    const normalizedUrl = normalizeImageUrl(imageUrl);

    if (showImageModal.value) {
        pendingTaskLogSendUrls.value.push(normalizedUrl);
        return;
    }

    try {
        await openImageOnCanvas(normalizedUrl);
    } catch (error) {
        console.error("TaskLogSend 이미지 표시 중 오류:", error);
    }
};

const checkNewTaskLogSendImage = async () => {
    const taskCode = props.card?.taskCode;
    if (!taskCode) {
        return;
    }

    try {
        const response = await Nova.request().get(
            `/nova-vendor/live-card/tasks/${taskCode}/latest-task-log-send`,
        );
        const latest = response.data.data;

        if (
            latest &&
            !shownImageIds.value.has(`send-${latest.id}`) &&
            (!lastImageCheck.value ||
                latest.created_at > lastImageCheck.value)
        ) {
            lastImageCheck.value = latest.created_at;
            shownImageIds.value.add(`send-${latest.id}`);
            showTaskLogSendImage(normalizeImageUrl(latest.image));
        }
    } catch (error) {
        console.error("TaskLogSend 이미지 확인 중 오류:", error);
    }
};

const showCapturedImage = async (imageUrl) => {
    try {
        console.log("showCapturedImage 함수가 호출되었습니다.");
        // 이미지 열림 여부 확인
        const response = await Nova.request().get(
            `/api/task-logs/${props.resourceId}/check-shown`,
        );

        console.log("이미지 열림 여부 : ", response.data);

        if (response.data.success && response.data.isShown) {
            console.log("이미 열린 이미지입니다.");
            return;
        }
        imageModalSource.value = "received";

        currentImage.value = imageUrl;
        showImageModal.value = true;

        // 이미지가 로드된 후 캔버스 설정
        await nextTick();
        const img = new Image();
        img.onload = () => {
            const canvasElement = canvas.value;
            canvasElement.width = img.width;
            canvasElement.height = img.height;

            ctx.value = canvasElement.getContext("2d");
            ctx.value.drawImage(img, 0, 0);
            baseImage.value = img;
        };
        img.src = imageUrl;

        // 이미지를 열린 것으로 표시
        await Nova.request().post(
            `/api/task-logs/${props.resourceId}/mark-shown`,
        );
    } catch (error) {
        console.error("이미지 처리 중 오류:", error);
    }
};

const closeImageModal = () => {
    showImageModal.value = false;
    currentImage.value = "";

    imageModalSource.value = null;

    shapes.value = [];
    currentPath.value = [];

    if (pendingTaskLogSendUrls.value.length > 0) {
        const nextUrl = pendingTaskLogSendUrls.value.shift();
        nextTick(() => showTaskLogSendImage(nextUrl));
    }
};

// Agora RTC 초기화 및 채널 참가
const joinChannel = async () => {
    try {
        await generateToken();

        console.log("joinChannel params:", {
            APP_ID,
            token,
            channelName,
            agoraUid,
        });

        if (!token || !channelName) {
            throw new Error(
                "토큰 또는 채널명이 없습니다. /api/agora/token 응답을 확인하세요.",
            );
        }

        // Agora SDK NG: join(appId, channel, token, uid)
        await rtc.client.join(APP_ID, channelName, token, agoraUid);

        isLocalConnected.value = true;

        rtc.localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
        rtc.localVideoTrack = await AgoraRTC.createCameraVideoTrack();

        await rtc.client.publish([rtc.localAudioTrack, rtc.localVideoTrack]);
        initConnect();

        isCameraOn.value = true;
        isMicrophoneOn.value = true;
        rtc.localVideoTrack.play("local-player");
    } catch (error) {
        console.error("토큰생성/채널참가 에러:", error);
        isLocalConnected.value = false;
    }
};

const leaveChannel = async () => {
    try {
        console.log("leaveChannel 함수가 호출되었습니다.");
        if (rtc.localAudioTrack) {
            console.log("오디오 트랙을 종료합니다.");
            rtc.localAudioTrack.close();
            isMicrophoneOn.value = false;
        }
        if (rtc.localVideoTrack) {
            console.log("비디오 트랙을 종료합니다.");
            rtc.localVideoTrack.close();
            isCameraOn.value = false;
        }

        if (rtc.client) {
            console.log("Agora 채널을 나갑니다.");
            await rtc.client.leave();
            console.log("Agora 채널을 성공적으로 나갔습니다.");
            isLocalConnected.value = false;
        }
    } catch (error) {
        console.error("채널 나가기 중 오류 발생:", error);
        throw error;
    }
};

const initConnect = () => {
    // 원격 사용자 스트림 수신
    rtc.client.on("user-published", async (user, mediaType) => {
        await rtc.client.subscribe(user, mediaType);
        console.log("mediaType : ", mediaType);
        if (mediaType === "video") {
            console.log("remote-player video : ", user);
            const remoteStream = user.videoTrack;
            remoteStream.play("remote-player"); // 원격 스트림 재생
            isRemoteConnected.value = true;
        }
    });

    rtc.client.on("user-unpublished", (user) => {
        console.log(`User ${user.uid} left the channel`);
        isRemoteConnected.value = false;
    });

    rtc.client.on("user-joined", () => {
        console.log("User joined the channel");
    });

    rtc.client.on("user-left", () => {
        console.log("User left the channel");
        isRemoteConnected.value = false;
    });
};

const generateToken = async () => {
    await Nova.request()
        .get(
            "/api/agora/token" +
                `/${props.card.userId}` +
                `/${props.resourceId}`,
        )
        .then((response) => {
            console.log("token : ", response.data.data);
            token = response.data.data.token;
            agoraUid = response.data.data.agora_uid;
            channelName = response.data.data.channel_name;
        })
        .catch((error) => {
            console.error(error);
        });
};

const setTool = (tool) => {
    currentTool.value = tool;
};

const startDrawing = (e) => {
    isDrawing.value = true;
    const rect = canvas.value.getBoundingClientRect();
    startX.value = e.clientX - rect.left;
    startY.value = e.clientY - rect.top;

    if (currentTool.value === "pen") {
        currentPath.value = [{ x: startX.value, y: startY.value }];
    }
};

const draw = (e) => {
    if (!isDrawing.value) return;

    const rect = canvas.value.getBoundingClientRect();
    const currentX = e.clientX - rect.left;
    const currentY = e.clientY - rect.top;

    if (currentTool.value === "pen") {
        // 펜 도구: 경로에 점 추가
        currentPath.value.push({ x: currentX, y: currentY });

        // 캔버스 다시 그리기
        redrawCanvas();

        // 현재 경로 그리기
        if (currentPath.value.length > 1) {
            ctx.value.strokeStyle = "#FF0000";
            ctx.value.lineWidth = 2;
            ctx.value.lineCap = "round";
            ctx.value.lineJoin = "round";
            ctx.value.beginPath();
            ctx.value.moveTo(currentPath.value[0].x, currentPath.value[0].y);
            for (let i = 1; i < currentPath.value.length; i++) {
                ctx.value.lineTo(
                    currentPath.value[i].x,
                    currentPath.value[i].y,
                );
            }
            ctx.value.stroke();
        }
    } else {
        // 이전 그림을 지우고 다시 그리기
        redrawCanvas();

        // 현재 그리는 도형
        ctx.value.strokeStyle = "#FF0000";
        ctx.value.lineWidth = 2;

        if (currentTool.value === "circle") {
            const radius = Math.sqrt(
                Math.pow(currentX - startX.value, 2) +
                    Math.pow(currentY - startY.value, 2),
            );
            ctx.value.beginPath();
            ctx.value.arc(startX.value, startY.value, radius, 0, 2 * Math.PI);
            ctx.value.stroke();
        } else if (currentTool.value === "rectangle") {
            ctx.value.strokeRect(
                startX.value,
                startY.value,
                currentX - startX.value,
                currentY - startY.value,
            );
        }
    }
};

const stopDrawing = (e) => {
    if (!isDrawing.value) return;

    const rect = canvas.value.getBoundingClientRect();
    const endX = e.clientX - rect.left;
    const endY = e.clientY - rect.top;

    if (currentTool.value === "pen") {
        // 펜 도구: 경로를 shapes에 저장
        if (currentPath.value.length > 1) {
            shapes.value.push({
                tool: "pen",
                path: [...currentPath.value],
            });
        }
        currentPath.value = [];
    } else {
        // 현재 도형 저장
        shapes.value.push({
            tool: currentTool.value,
            startX: startX.value,
            startY: startY.value,
            endX,
            endY,
        });
    }

    isDrawing.value = false;
};

const drawShape = (shape) => {
    ctx.value.strokeStyle = "#FF0000";
    ctx.value.lineWidth = 2;

    if (shape.tool === "pen") {
        // 펜 도구: 경로 그리기
        if (shape.path && shape.path.length > 1) {
            ctx.value.lineCap = "round";
            ctx.value.lineJoin = "round";
            ctx.value.beginPath();
            ctx.value.moveTo(shape.path[0].x, shape.path[0].y);

            for (let i = 1; i < shape.path.length; i++) {
                ctx.value.lineTo(shape.path[i].x, shape.path[i].y);
            }

            ctx.value.stroke();
        }
    } else if (shape.tool === "circle") {
        const radius = Math.sqrt(
            Math.pow(shape.endX - shape.startX, 2) +
                Math.pow(shape.endY - shape.startY, 2),
        );
        ctx.value.beginPath();
        ctx.value.arc(shape.startX, shape.startY, radius, 0, 2 * Math.PI);
        ctx.value.stroke();
    } else if (shape.tool === "rectangle") {
        ctx.value.strokeRect(
            shape.startX,
            shape.startY,
            shape.endX - shape.startX,
            shape.endY - shape.startY,
        );
    }
};

const undoLastShape = () => {
    if (shapes.value.length > 0) {
        shapes.value.pop();
        redrawCanvas();
    }
};

const redrawCanvas = () => {
    // 캔버스 초기화
    ctx.value.clearRect(0, 0, canvas.value.width, canvas.value.height);

    // 원본 이미지 다시 그리기
    if (baseImage.value) {
        ctx.value.drawImage(baseImage.value, 0, 0);
    }

    // 저장된 모든 도형 다시 그리기
    shapes.value.forEach((shape) => drawShape(shape));
};

const saveDrawing = async () => {
    try {
        const imageData = canvas.value.toDataURL("image/png");

        ignoreNextCapturedImage.value = true;

        const response = await Nova.request().post(
            `/api/task/upload/image/${props.resourceId}`,
            {
                image: imageData,
            },
        );

        if (response.data.status === "OK") {
            const savedImage = response.data.data;

            // 내가 저장한 이미지는 다시 열리지 않게 처리
            if (savedImage?.id) {
                shownImageIds.value.add(savedImage.id);
            }

            if (savedImage?.created_at) {
                lastImageCheck.value = savedImage.created_at;
            }

            Nova.success("이미지가 성공적으로 저장되었습니다.");

            closeImageModal();

            setTimeout(() => {
                ignoreNextCapturedImage.value = false;
            }, 1500);
        }
    } catch (error) {
        ignoreNextCapturedImage.value = false;

        console.error("이미지 저장 중 오류:", error);
        Nova.error("이미지 저장에 실패했습니다.");
    }
};
const toggleCamera = async () => {
    try {
        if (isCameraOn.value) {
            // 카메라 끄기
            if (rtc.localVideoTrack) {
                await rtc.localVideoTrack.stop();
                await rtc.localVideoTrack.setMuted(true);
                isCameraOn.value = false;
            }
        } else {
            // 카메라 켜기
            if (rtc.localVideoTrack) {
                await rtc.localVideoTrack.play("local-player");
                await rtc.localVideoTrack.setMuted(false);
                isCameraOn.value = true;
            }
        }
    } catch (error) {
        console.error("카메라 토글 중 오류:", error);
        Nova.error("카메라 상태 변경에 실패했습니다.");
    }
};

const toggleMicrophone = async () => {
    try {
        if (isMicrophoneOn.value) {
            await rtc.localAudioTrack.setMuted(true);
            isMicrophoneOn.value = false;
        } else {
            await rtc.localAudioTrack.setMuted(false);
            isMicrophoneOn.value = true;
        }
    } catch (error) {
        console.error("마이크 토글 중 오류:", error);
        Nova.error("마이크 상태 변경에 실패했습니다.");
    }
};

// 녹화 시작
const startRecording = async () => {
    try {
        console.log("녹화 시작 호출");
        const response = await Nova.request().post("/api/recording/start", {
            channel_name: channelName,
            uid: agoraUid,
            token: token,
        });

        if (response.data.success) {
            isRecording.value = true;
            recordingResourceId.value = response.data.resourceId;
            recordingSid.value = response.data.sid;
            console.log("녹화 시작 성공:", response.data);
        } else {
            console.error("녹화 시작 실패:", response.data.message);
        }
    } catch (error) {
        console.error("녹화 시작 중 오류:", error);
    }
};

// 녹화 중지
const stopRecording = async () => {
    try {
        const response = await Nova.request().post("/api/recording/stop", {
            channelName,
            uid: agoraUid,
            resourceId: recordingResourceId.value,
            sid: recordingSid.value,
        });

        if (response.data.success) {
            const recordingInfo = response.data.recordingInfo;
            // 녹화 파일 경로를 meetings 테이블에 저장
            await Nova.request().post(
                `/nova-vendor/live-card/meetings/${props.resourceId}/recording`,
                {
                    recordings_path: recordingInfo.fileList[0].fileName,
                },
            );
            isRecording.value = false;
            Nova.success("녹화가 완료되었습니다.");
        }
    } catch (error) {
        console.error("녹화 중지 중 오류:", error);
        Nova.error("녹화 중지에 실패했습니다.");
    }
};

// 키보드 이벤트 핸들러
const handleKeyDown = (e) => {
    // ESC 키로 전체화면 종료
    if (e.key === "Escape" && isFullscreen.value) {
        leaveMeeting();
    }
};

// 채팅 토글
const toggleChat = () => {
    showChat.value = !showChat.value;
};

// 이미지 선택
const selectImage = () => {
    imageInput.value.click();
};

// 이미지 업로드 처리
const handleImageUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // 이미지 파일만 허용
    if (!file.type.startsWith("image/")) {
        Nova.error("이미지 파일만 업로드 가능합니다.");
        return;
    }

    try {
        // 파일을 base64로 변환 후 즉시 편집 모달 오픈
        const reader = new FileReader();
        reader.onload = async (e) => {
            const imageData = e.target.result;
            imageModalSource.value = "attach";

            currentImage.value = imageData;
            showImageModal.value = true;

            // 이미지가 로드된 후 캔버스 설정
            await nextTick();
            const img = new Image();
            img.onload = () => {
                const canvasElement = canvas.value;
                canvasElement.width = img.width;
                canvasElement.height = img.height;

                ctx.value = canvasElement.getContext("2d");
                ctx.value.drawImage(img, 0, 0);
                baseImage.value = img;
                shapes.value = [];
                currentPath.value = [];
            };
            img.src = imageData;
        };
        reader.readAsDataURL(file);
    } catch (error) {
        console.error("이미지 업로드 중 오류:", error);
        Nova.error("이미지 업로드에 실패했습니다.");
    }

    // 입력 필드 초기화
    event.target.value = "";
};

// 날짜 포맷
const formatToLocalTime = (inputDate) => {
    const date = new Date(inputDate);

    return new Intl.DateTimeFormat("default", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
    }).format(date);
};

// 메시지 로드
const loadMessages = async () => {
    try {
        const response = await Nova.request().get(
            `/api/messages/${props.resourceId}`,
        );
        messages.value = response.data.data || [];

        // 스크롤을 맨 아래로
        await nextTick();
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop =
                messagesContainer.value.scrollHeight;
        }
    } catch (error) {
        console.error("메시지 로드 실패:", error);
    }
};

// 메시지 전송
const sendMessage = async () => {
    if (isSending.value) return;

    const content = newMessage.value.trim();
    if (content === "") return;

    isSending.value = true;
    newMessage.value = "";

    try {
        await Nova.request().post("/nova-vendor/chat-card/message", {
            meeting_id: props.resourceId,
            content,
        });

        await loadMessages();
    } catch (error) {
        await loadMessages();

        const saved = messages.value.some(
            (m) =>
                m.content === content &&
                String(m.user_id) === String(props.card?.userId),
        );

        if (!saved) {
            console.warn("메시지 전송 실패:", error);
            newMessage.value = content;
        }
    } finally {
        isSending.value = false;

        await nextTick();

        messageInput.value?.focus();
    }
};
</script>

<style scoped>
.remote-player-frame {
    width: min(100%, 960px);
    margin: 0 auto;
}

.local-player-frame {
    width: clamp(160px, 32vw, 320px);
}

#remote-player {
    width: 100%;
    aspect-ratio: 16 / 9;
    max-height: min(56vh, calc(100vh - 260px));
    background-color: #1a202c;
    border-radius: 0.5rem;
    overflow: hidden;
}

#local-player {
    width: 100%;
    aspect-ratio: 16 / 9;
    max-height: min(24vh, 180px);
    background-color: #1a202c;
    border-radius: 0.5rem;
    overflow: hidden;
}

#remote-player :deep(video),
#remote-player :deep(canvas),
#local-player :deep(video),
#local-player :deep(canvas) {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
}

@media (max-width: 640px) {
    #remote-player {
        max-height: min(48vh, calc(100vh - 300px));
    }

    #local-player {
        max-height: min(20vh, 140px);
    }
}
</style>
