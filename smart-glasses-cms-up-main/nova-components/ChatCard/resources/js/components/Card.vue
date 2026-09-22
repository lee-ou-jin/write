<template>
    <Card
        class="flex items-center justify-center w-full"
        style="
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        "
    >
        <div class="w-full p-6">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">
                메시지 전송
            </h1>
            <div
                class="chat-messages overflow-auto mb-6"
                style="
                    height: 400px;
                    background-color: #f4f7f9;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    padding: 15px;
                "
            >
                <div v-for="message in messages" :key="message.id" class="mb-4">
                    <strong class="text-black-600"
                        >[{{ this.formatToLocalTime(message.created_at) }}]
                    </strong>
                    <strong class="text-indigo-600"
                        >{{ this.card.user.name }} :
                    </strong>
                    <span class="text-gray-700">{{ message.content }}</span>
                </div>
            </div>
            <div>
                <input
                    v-model="newMessage"
                    @keyup.enter="sendMessage"
                    type="text"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                    placeholder="메시지 입력 후 Enter 키를 누르세요"
                />
            </div>
        </div>
    </Card>
</template>

<script>
// import Echo from "laravel-echo";
// import io from 'socket.io-client';

export default {
    props: ["card", "resourceId"],
    data() {
        return {
            messages: [],
            newMessage: "",
            echo: null,
        };
    },

    mounted() {
        // this.echo = new Echo({
        //     broadcaster: 'socket.io',
        //     host: window.location.hostname + ':6001', // WebSocket 서버 주소
        // });
        //
        // this.echo.channel('chat-room')
        //     .listen('MessageSent', (e) => {
        //         this.messages.push({
        //             id: e.message.id,
        //             user_name: e.user.name,
        //             content: e.message.content,
        //         });
        //     });
        //
        this.loadMessages();
        console.log("dd");

        console.log("resourceId : ", this.resourceId);
        console.log("card : ", this.card.user);
        console.log("message : ", this.card.messages);
    },

    methods: {
        formatToLocalTime(inputDate) {
            const date = new Date(inputDate);

            // 사용자의 로컬 시간대와 기본 포맷 적용
            return new Intl.DateTimeFormat("default", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
                hour12: false, // 24시간 형식 사용
            }).format(date);
        },
        loadMessages() {
            if (this.messages.length === 0) {
                this.messages = this.card.messages;
                return;
            }

            Nova.request()
                .get("/nova-vendor/chat-card/messages/" + this.resourceId)
                .then((response) => {
                    this.messages = response.data.data;
                    // console.log(response.data.data);

                    console.log(
                        "message time : ",
                        this.formatToLocalTime(this.messages[0].created_at),
                    );
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        sendMessage() {
            if (this.newMessage.trim() === "") return;

            // axios.post('/nova-vendor/chat-card/messages', {
            //     content: this.newMessage,
            // })
            //     .then(response => {
            //         this.newMessage = '';
            //     })
            //     .catch(error => {
            //         console.error(error);
            //     });
            // axios.post('/nova-vendor/chat-card/message', {
            //     'meeting_id' : 2,
            //     'user_id' : '9db4e595-3aac-4fd5-8a2f-b05302da68a8',
            //     'content' : this.newMessage,
            // })
            //     .then(response => {
            //         this.newMessage = '';
            //     })
            //     .catch(error => {
            //         console.error(error);
            //     });

            // nova request post
            Nova.request()
                .post("/nova-vendor/chat-card/message", {
                    meeting_id: this.resourceId,
                    user_id: this.card.user.id,
                    content: this.newMessage,
                })
                .then((response) => {
                    console.log(response);
                    this.newMessage = "";

                    this.loadMessages();
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};
</script>

<style scoped>
.chat-messages {
    font-family: "Roboto", sans-serif;
    line-height: 1.5;
}

.Card {
    width: 100%; /* 카드가 Nova의 기본 제한 너비를 초과 */
    display: flex;
    justify-content: center;
    align-items: center;
}

.card-content {
    flex: 1;
}
</style>
