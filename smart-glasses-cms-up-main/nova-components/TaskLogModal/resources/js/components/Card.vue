<template>
    <div>
        <!-- 카드 UI는 비워 두고, 수신 이미지 편집 모달만 표시 -->
        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75"
            style="z-index: 100"
        >
            <div
                class="bg-gray-800 rounded-lg shadow-2xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col"
            >
                <div class="p-6 border-b border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-100">
                            스마트글래스 수신 이미지
                        </h3>
                        <div class="flex items-center space-x-2">
                            <button
                                @click="setTool('pen')"
                                :class="toolButtonClass('pen')"
                                class="px-3 py-2 rounded-lg text-white text-sm"
                            >
                                Pen
                            </button>
                            <button
                                @click="setTool('circle')"
                                :class="toolButtonClass('circle')"
                                class="px-3 py-2 rounded-lg text-white text-sm"
                            >
                                Circle
                            </button>
                            <button
                                @click="undoLastShape"
                                class="px-3 py-2 rounded-lg bg-gray-600 hover:bg-gray-700 text-white text-sm"
                            >
                                Undo
                            </button>
                            <button
                                @click="closeModal"
                                class="text-gray-400 hover:text-gray-200"
                            >
                                ✕
                            </button>
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
                            style="max-width: 100%; max-height: 100%"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Echo from "laravel-echo";
import Pusher from "pusher-js";

export default {
    name: "task-log-modal",
    props: ["card"],

    data() {
        return {
            showModal: false,
            latestImageUrl: null,
            pendingImageUrls: [],
            shownSendIds: new Set(),
            channelName: null,
            currentTool: "pen",
            isDrawing: false,
            startX: 0,
            startY: 0,
            shapes: [],
            currentPath: [],
            ctx: null,
            baseImage: null,
        };
    },

    mounted() {
        if (this.card?.echo) {
            this.initEcho(this.card.echo);
        }
        this.listenForTaskLogSendCaptured();
    },

    beforeUnmount() {
        if (window.Echo && this.channelName) {
            window.Echo.leave(this.channelName);
        }
    },

    methods: {
        initEcho(config) {
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
        },

        listenForTaskLogSendCaptured() {
            const taskCode = this.card?.taskCode;

            if (!taskCode) {
                console.warn("TaskLogModal: taskCode가 없습니다.");
                return;
            }

            if (!window.Echo) {
                console.warn(
                    "TaskLogModal: Echo가 없습니다. BROADCAST_DRIVER와 reverb 설정을 확인하세요.",
                );
                return;
            }

            this.channelName = `task.${taskCode}`;

            window.Echo.channel(this.channelName).listen(
                "TaskLogSendCaptured",
                (e) => {
                    const imageUrl = e.imageUrl;
                    const sendId = e.taskLogSendId ?? imageUrl;

                    if (!imageUrl || this.shownSendIds.has(sendId)) {
                        return;
                    }

                    this.shownSendIds.add(sendId);
                    this.openImageModal(imageUrl);
                },
            );
        },

        openImageModal(imageUrl) {
            if (this.showModal) {
                this.pendingImageUrls.push(imageUrl);
                return;
            }

            this.latestImageUrl = imageUrl;
            this.showModal = true;
            this.$nextTick(() => this.loadImageToCanvas(imageUrl));
        },

        loadImageToCanvas(imageUrl) {
            const canvas = this.$refs.canvas;
            if (!canvas) {
                return;
            }

            const img = new Image();
            img.crossOrigin = "anonymous";
            img.onload = () => {
                canvas.width = img.width;
                canvas.height = img.height;
                this.ctx = canvas.getContext("2d");
                this.ctx.drawImage(img, 0, 0);
                this.baseImage = img;
                this.shapes = [];
                this.currentPath = [];
            };
            img.onerror = () => {
                console.error("이미지 로드 실패:", imageUrl);
            };
            img.src = imageUrl;
        },

        closeModal() {
            this.showModal = false;
            this.latestImageUrl = null;
            this.shapes = [];
            this.currentPath = [];

            if (this.pendingImageUrls.length > 0) {
                const next = this.pendingImageUrls.shift();
                this.$nextTick(() => this.openImageModal(next));
            }
        },

        toolButtonClass(tool) {
            return this.currentTool === tool
                ? "bg-blue-600 hover:bg-blue-700"
                : "bg-gray-600 hover:bg-gray-700";
        },

        setTool(tool) {
            this.currentTool = tool;
        },

        startDrawing(e) {
            this.isDrawing = true;
            const rect = this.$refs.canvas.getBoundingClientRect();
            this.startX = e.clientX - rect.left;
            this.startY = e.clientY - rect.top;

            if (this.currentTool === "pen") {
                this.currentPath = [{ x: this.startX, y: this.startY }];
            }
        },

        draw(e) {
            if (!this.isDrawing || !this.ctx) {
                return;
            }

            const rect = this.$refs.canvas.getBoundingClientRect();
            const currentX = e.clientX - rect.left;
            const currentY = e.clientY - rect.top;

            this.redrawCanvas();

            this.ctx.strokeStyle = "#FF0000";
            this.ctx.lineWidth = 2;

            if (this.currentTool === "pen") {
                this.currentPath.push({ x: currentX, y: currentY });
                if (this.currentPath.length > 1) {
                    this.ctx.beginPath();
                    this.ctx.moveTo(
                        this.currentPath[0].x,
                        this.currentPath[0].y,
                    );
                    for (let i = 1; i < this.currentPath.length; i++) {
                        this.ctx.lineTo(
                            this.currentPath[i].x,
                            this.currentPath[i].y,
                        );
                    }
                    this.ctx.stroke();
                }
            } else if (this.currentTool === "circle") {
                const radius = Math.sqrt(
                    Math.pow(currentX - this.startX, 2) +
                        Math.pow(currentY - this.startY, 2),
                );
                this.ctx.beginPath();
                this.ctx.arc(
                    this.startX,
                    this.startY,
                    radius,
                    0,
                    2 * Math.PI,
                );
                this.ctx.stroke();
            }
        },

        stopDrawing(e) {
            if (!this.isDrawing) {
                return;
            }

            const rect = this.$refs.canvas.getBoundingClientRect();
            const endX = e.clientX - rect.left;
            const endY = e.clientY - rect.top;

            if (this.currentTool === "pen" && this.currentPath.length > 1) {
                this.shapes.push({
                    tool: "pen",
                    path: [...this.currentPath],
                });
            } else if (this.currentTool === "circle") {
                this.shapes.push({
                    tool: "circle",
                    startX: this.startX,
                    startY: this.startY,
                    endX,
                    endY,
                });
            }

            this.currentPath = [];
            this.isDrawing = false;
            this.redrawCanvas();
        },

        drawShape(shape) {
            this.ctx.strokeStyle = "#FF0000";
            this.ctx.lineWidth = 2;

            if (shape.tool === "pen" && shape.path?.length > 1) {
                this.ctx.beginPath();
                this.ctx.moveTo(shape.path[0].x, shape.path[0].y);
                for (let i = 1; i < shape.path.length; i++) {
                    this.ctx.lineTo(shape.path[i].x, shape.path[i].y);
                }
                this.ctx.stroke();
            } else if (shape.tool === "circle") {
                const radius = Math.sqrt(
                    Math.pow(shape.endX - shape.startX, 2) +
                        Math.pow(shape.endY - shape.startY, 2),
                );
                this.ctx.beginPath();
                this.ctx.arc(
                    shape.startX,
                    shape.startY,
                    radius,
                    0,
                    2 * Math.PI,
                );
                this.ctx.stroke();
            }
        },

        undoLastShape() {
            this.shapes.pop();
            this.redrawCanvas();
        },

        redrawCanvas() {
            if (!this.ctx || !this.$refs.canvas) {
                return;
            }

            this.ctx.clearRect(
                0,
                0,
                this.$refs.canvas.width,
                this.$refs.canvas.height,
            );

            if (this.baseImage) {
                this.ctx.drawImage(this.baseImage, 0, 0);
            }

            this.shapes.forEach((shape) => this.drawShape(shape));
        },
    },
};
</script>
