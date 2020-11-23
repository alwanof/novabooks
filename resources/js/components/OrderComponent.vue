<template>
    <div class="container text-left">
        <div class="row">

            <div class="col-12">
                {{Date(feed.created_at)}}
            </div>
            <div class="col-4">
                <small>Order No:</small><br>
                {{feed.id}}
            </div>
            <div class="col-8 h4">
                <span class="badge badge-danger float-right">{{statusLabel(feed.status)}}</span>
            </div>
            <div class="col-12">
                    <div class="progress" v-if="feed.status==0" >
                        <div class="progress-bar progress-bar-striped bg-secondary" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress" v-if="feed.status==1" >
                        <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress" v-if="feed.status==91" >
                        <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress" v-if="feed.status==2" >
                        <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress" v-if="feed.status==21" >
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                </div>
        </div>
        <div class="row">
            <div class="col-8">

                <div class="col-12" v-show="feed.from_address">
                    <small>From:</small><br>
                    {{feed.from_address}}
                </div>
                <div class="col-12" v-show="feed.to_address">
                    <small>To:</small><br>
                    {{feed.to_address}}
                </div>

            </div>
            <div class="col-4 text-center">

                   <div v-if="feed.status==21">
                       <img class="img-thumbnail rounded-circle" :src="'/storage/'+feed.driver.avatar" alt="" width="64" >
                    <div>{{feed.driver.name}}</div>
                    <div>
                        <a :href="'tel:'+feed.driver.phone">
                            <i class="fas fa-phone-square-alt text-success"></i>
                        </a>
                    </div>
                   </div>

            </div>



        </div>
        <div class="row mt-3" v-if="feed.status==3">
            <div class="col-9">
                   <button type="button" @click="approve()" class="btn btn-outline-success btn-sm btn-block mb-1">Approve</button>

                   <button type="button" @click="reject()" class="btn btn-outline-danger btn-sm btn-block">Reject</button>
                </div>
                <div class="col-3 p-2 h3">
                    <span class="badge badge-dark" v-show="feed.offer">{{feed.offer}} {{office.settings.currency}}</span>
                </div>
        </div>
         <div class="row " v-if="cancelValid(feed.status)">

                <div class="col mt-3">
                    <button type="button" @click="cancel()" class="btn btn-dark btn-block">Cancel</button>
                </div>
            </div>
    </div>
</template>

<script>
  // Parse Here
const Parse = require('parse');
Parse.initialize("REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV", "VSDqMVaQWg5HDnFM0oAezLdeDRdfMvdZKhgW7THn");
Parse.serverURL = "https://smartaxi.b4a.io";

var Client = new Parse.LiveQueryClient({
    applicationId: 'REhnNlzTuS88KmmKaNuqwWZ3D3KNYurvNIoWHdYV',
    serverURL: 'wss://' + 'smartaxi.b4a.io', // Example: 'wss://livequerytutorial.back4app.io'
    javascriptKey: 'VSDqMVaQWg5HDnFM0oAezLdeDRdfMvdZKhgW7THn'
});

const query = new Parse.Query("Stream");
query.equalTo("model", "Order");
Client.open();
var subscription = Client.subscribe(query);

var FCM = require('fcm-node');

    export default {
        name:"Order-Component",
        props:['office','agent','order'],
        data() {
            return {
                feed:null,
            }
        },
        created() {
            this.feed=this.order;
            this.listen();
            this.sendMyFCM();

        console.log('Working ..');

        },
        methods: {
            sendMyFCM(){
                var serverKey = 'AAAAL7epsrw:APA91bGgOmwkI-dPOJAI86XP9_LtipbeeshHEgkdGK_r7gLQEupcza9ApKgr1T97mEBwn8psdaNmgXRi4UtxAGju15rVBwlB3wcXRXWBbLglwtcJeeHrFwgO_arXK4-KTPszqjCYZKfM'; //put your server key here
                var fcm = new FCM(serverKey);
                var message = { //this may vary according to the message type (single recipient, multicast, topic, et cetera)
                    to: 'ef1jFZERQaK4ozAk1TRtBg:APA91bGuXg4RH56jmgPeZmQGR_7juJSNIDw5U7j7YnLJUtUbpLg5Mi2PbLY3V6OqPJeOdm89t7GYyXMzq6qAao5wvfv5PZm8VRtYtRIPncxi51Nmo--izNh-WRbU5pjt0ixC6iGFrQa9',
                    notification: {
                        title: 'Title of your push notification',
                        body: 'Body of your push notification'
                    },
                    data: {  //you can send only notification or only data(or include both)
                        my_key: 'my value',
                        my_another_key: 'my another value'
                    }
                };
                fcm.send(message, function(err, response){
                    if (err) {
                        console.log("Something has gone wrong!");
                    } else {
                        console.log("Successfully sent with response: ", response);
                    }
                });

            },
            listen(){


                subscription.on("create", (feedDoc) => {
                    console.log(feedDoc.attributes);
                    let index = (this.feed.id==feedDoc.attributes.pid);
                    if(index){
                        axios
                        .get( '/api/orders/get/' + feedDoc.attributes.pid)
                        .then((res) => {
                            this.feed=res.data;
                        });

                    }

                });
            },
            approve(){
                axios.get('/api/order/customer/approve/'+this.feed.id).then((res) => {
                    //console.log(res.data);
                });
            },
            reject(){
                axios.get('/api/order/customer/reject/'+this.feed.id).then((res) => {
                    //console.log(res.data);
                    window.location.href = '/taxi/'+this.office.email;
                });
            },
            cancel(){

                axios.get('/api/orders/cancel/'+this.feed.id).then((res) => {
                    window.location.href = '/taxi/'+this.office.email;
                });
            },
            cancelValid(status){
                var valid=[0,1,12,2,3];
                return valid.includes(status);

            },
            statusLabel(status){
                var label='-';
                switch (status) {
                    case 0:
                        label='New';
                        break;
                    case 1:
                        label='Accepted';
                        break;
                    case 2:
                        label='Waiting Driver Approve';
                        break;
                    case 21:
                        label='Proccessing..';
                        break;
                    case 3:
                        label='Waiting Customer Approve';
                        break;
                    case 9:
                        label='Done';
                        break;
                    case 91:
                        label='Office Reject';
                        break;
                    case 92:
                        label='Customer Reject';
                        break;
                    case 93:
                        $label='No-Resp from Office';
                        break;
                    case 94:
                        label='No-Resp from Customer';
                        break;
                    case 95:
                        label='Canceled';
                        break;
                    case 99:
                        label='Canceled from Customer';
                        break;


                    default:

                        break;
                }
                return label;
            },
            statusIcon(status){
                var icon='far fa-circle';
                switch (icon) {
                    case 0:
                        icon='fas fa-circle';
                        break;
                    case 1:
                        icon='fas fa-check-circle';
                        break;
                    case 2:
                        icon='far fa-pause-circle';
                        break;
                    case 21:
                        icon='fas fa-car';
                        break;
                    case 3:
                        icon='fas fa-user-clock';
                        break;
                    case 9:
                        icon='fas fa-check-double';
                        break;
                    case 91:
                        icon='fas fa-minus-circle';
                        break;
                    case 92:
                        icon='far fa-times-circle';
                        break;
                    case 93:
                        $icon='far fa-times-circle';
                        break;
                    case 94:
                        icon='fab fa-gg-circle';
                        break;
                    case 95:
                        icon='fab fa-gg-circle';
                        break;
                    case 99:
                        icon='far fa-times-circle';
                        break;

                    default:
                        break;
                }
                return icon;
            },
        },
    }
</script>
