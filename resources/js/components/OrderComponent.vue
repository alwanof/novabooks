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
                        <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
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

                   <div v-if="feed.driver">
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
         <div class="row ">

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

                const query = new Parse.Query("Stream");
                query.equalTo("model", "Order");
                Client.open();
                var subscription = Client.subscribe(query);
                subscription.on("create", (feedDoc) => {
                    console.log(feedDoc);
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
                    console.log(res.data);
                });
            },
            reject(){
                axios.get('/api/order/customer/reject/'+this.feed.id).then((res) => {
                    console.log(res.data);
                });
            },
            cancel(){
                axios.get('/api/orders/cancel/'+this.feed.id).then((res) => {
                    console.log(res.data);
                });
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
                    case 99:
                        label='Canceled from Customer';
                        break;

                    default:
                        break;
                }
                return label;
            },
        },
    }
</script>
