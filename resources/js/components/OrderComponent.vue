<template>
    <div class="container text-left">
        <div class="row">
            <div class="col-12 text-center">
                <i :class="statusIcon(feed.status)+' fa-2x'"></i> <span class="text-danger lead">{{statusLabel(feed.status)}}</span>
            </div>

            <div class="col-8">
                <div class="text-muted">DATE</div>
                {{feed.created_at}}
            </div>
            <div class="col-4">
                <div class="text-muted">Order No</div>
                {{feed.id}}


            </div>

            <div class="col-12 mt-1">
                    <div class="progress" v-if="feed.status==0" style="height:0.5rem">
                        <div class="progress-bar progress-bar-striped bg-secondary" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress" v-if="feed.status==1" style="height:0.5rem" >
                        <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress" v-if="feed.status==91" style="height:0.5rem" >
                        <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress" v-if="feed.status==2" style="height:0.5rem" >
                        <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="progress" v-if="feed.status==21" style="height:0.5rem" >
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="text-muted">
                    Your Trip
                    <span class="float-right h4 text-danger font-bold" v-show="feed.offer">{{feed.offer}} {{office.settings.currency}}</span>
                </div>
            </div>
            <div class="col-12">

                <div class="col-12" v-show="feed.from_address">
                    <div class="text-danger">From</div>
                    {{feed.from_address}}
                </div>
                <div class="col-12" v-show="feed.to_address">
                     <div class="text-danger">To</div>
                    {{feed.to_address}}
                </div>

            </div>


        </div>
        <div class="row mt-4 py-2 border border-secondary" v-if="feed.status==21">
                <div class="col-12 text-muted">Driver Details</div>
                <div class="col-3">
                    <img class="img-thumbnail rounded-circle" :src="'/storage/'+feed.driver.avatar" alt="" width="64" >
                </div>
                <div class="col-9">
                    <div class="text-muted">Name</div>
                    {{feed.driver.name}}
                    <a :href="'tel:'+feed.driver.phone" class="btn btn-lg btn-outline-success float-right">
                            <i class="fas fa-phone-square-alt"></i>
                        </a>
                </div>
                <div class="col-6">
                    <div class="text-muted">Car Model</div>
                     {{feed.driver.taxi}}
                </div>
                <div class="col-6">
                    <div class="text-muted">Car Color</div>
                     {{feed.driver.taxiColor}}
                </div>

        </div>

        <div class="row mt-3" v-if="feed.status==3">
            <div class="col-12">
                   <button type="button" @click="approve()" class="btn btn-outline-success btn-sm btn-block mb-1">Approve</button>

                   <button type="button" @click="reject()" class="btn btn-outline-danger btn-sm btn-block">Reject</button>
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
        },
        methods: {
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
                switch (status) {
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


