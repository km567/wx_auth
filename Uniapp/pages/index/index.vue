<template> 
	<view class="content">
		<view class="data">
			<view class="box" v-if="obj.headpic">
				<view class="head" style="display: flex;">
					<span style="margin-top: 15px;">用户头像:</span>
					<image class="img" :src="obj.headpic"></image>
					<view class="name">昵称:{{obj.nickName}}</view>
				</view>
			</view> 
			<view class="box" v-if="obj.token">
				<view class="token">Token:{{obj.token}}</view>
			</view>
			<view class="box" v-if="obj.address">
				<view class="address">收货地址:{{obj.address}}</view>
			</view>
			<view class="box" v-if="obj.location.latitude">
				<view class="location">
					<view>所在位置:</view>
					<view class="lat">
						<view class="lat_1">
							纬度：{{obj.location.latitude}}
						</view>
						<view class="lat_2">
							经度：{{obj.location.longitude}}
						</view>
					</view>
				</view>
			</view>
			<view class="box" v-if="obj.back_location">
				<view class="back_location">后台获取位置状态:{{obj.back_location}}</view>
			</view>
		</view>
		<audio :src="audio.src" id="myAudio"></audio>
		<view class="btn">
			<button class="btn_1" type="primary" @click="onAuth">静默授权</button>
			<button class="btn_1" type="primary" @click="onUserInfo">头像</button>
			<!-- #ifdef MP-WEIXIN --> 
			<button class="btn_1" type="primary" @click="getAddress">收货地址</button>
			<button class="btn_1" type="primary" open-type="getPhoneNumber" @getphonenumber="getPhoneNumber">授权电话</button>
			<button class="btn_1" type="primary" @click="get_location">位置</button>
			<button class="btn_1" type="primary" @click="ready_pay">支付</button>
			
			<view style="padding:20px 0;">微信后台执行的任务（关闭微信后仍继续）</view>
			<button class="btn_1" type="primary" @click="background_location">微信持续获取位置</button> 
			<button class="btn_1" type="primary" @click="start">开始语音播报</button>
			<button class="btn_1" type="primary" v-if="loop" @click="loop=false">暂停语音播报</button>
			<!-- #endif -->

			<!-- #ifdef H5 -->
			<view>公众号：</view>
			<button class="btn_1" type="primary">获取微信地址</button>
			<!-- #endif -->

		</view>

		<!-- <view v-for="(item,index) in list">
			{{index}}:--{{item}}
		</view> -->

		<XcxAuth :auth="auth" @get_userinfo="userinfo"></XcxAuth>
	</view>
</template>

<script>
	import XcxAuth from "@/components/wx_auth/xcx_auth.vue"
	import {
		XcxToken
	} from '@/common/xcx_token.js'
	import {
		WxToken
	} from '@/common/wx_token.js'
	var xcxtoken = new XcxToken();
	var wxtoken = new WxToken();
	
	export default {
		data() {
			return {
				auth: {
					is_name: false,
					is_address: false,
					is_phone: false,
				},
				loop:true,	//语音播报开发
				list: '',
				obj: {
					token: '',
					headpic: '',
					address: '',
					phone: '',
					nickName: '',
					location: {
						latitude: '', //纬度
						longitude: '' //经度
					},
					back_location: ''
				},
				is_pause:1
				
			}
		},
		components: {
			XcxAuth
		},
		onLoad() { 
			console.log('截取code获取token')
			wxtoken.spliceCode()
			
		},
		methods: {
			onAuth() {
				// #ifdef MP-WEIXIN
				xcxtoken.verify(); //小程序获取token 
				// this.obj.token = token
				let cache = uni.getStorageSync('token')
				this.obj.token = cache
				// #endif

				// #ifdef H5
				//微信公众号获取token -必须是认证的服务号 
				wxtoken.verify(); //静默获取openid					
				// #endif
			},
			onUserInfo() {
				// #ifdef MP-WEIXIN
				this.auth.is_name = !this.auth.is_name
				// #endif

				// #ifdef H5
				wxtoken.verify('userinfo'); //手动授权获取openid和头像昵称
				// #endif
			},
			getPhoneNumber(e) {
				console.log(e)
				console.log(e.detail.errMsg)
				console.log(e.detail.iv)
				console.log(e.detail.encryptedData)
				let obj = {}
				obj.iv = e.detail.iv
				obj.encryptedData = e.detail.encryptedData
				//然后在第三方服务端结合 session_key 以及 app_id 进行解密获取手机号

				this.$api.http.post('auth/decryptToGetMobile',obj).then(res=>{
					console.log(res)
				})
			},
			getAddress() {
				const that = this
				let address = ''
				uni.chooseAddress({
					success(res) {
						console.log(res)
						address = res
					},
					complete(res) {
						that.obj.address = res.provinceName + res.cityName + res.countyName + res.detailInfo
					}
				})
				console.log(address)

			},
			userinfo(e) {
				console.log(e)
				this.obj.headpic = e.avatarUrl
				this.obj.nickName = e.nickName
			},
			async ready_pay(){
				const order_id='16'
				const pay_data=await this.$api.http.post('order/pay/pre_order?XDEBUG_SESSION_START=14152', {
					id: order_id
				}).then(res => {
					console.log('pay:', res)
					return res
				})				
				await this.pay(pay_data) 
			},
			pay(data) {
				const order_id = this.order_id
			    uni.requestPayment({
			    	provider:"wxpay", 
			    	timeStamp: data.timeStamp,
			    	nonceStr: data.nonceStr,
			    	package: data.package,
			    	signType: data.signType,
			    	paySign: data.paySign,
			    	success: function (res) {  
			    		console.log('支付成功:' + JSON.stringify(res)); 
			    	},
			    	fail: function (err) { 						
			    		console.log('放弃支付:' + JSON.stringify(err)); 
			    	}
			    });
			},			
			
			get_location() {
				const that = this
				wx.getLocation({
					type: 'wgs84',
					success(res) {
						console.log(res)
						const latitude = res.latitude //纬度
						const longitude = res.longitude //经度
						const speed = res.speed
						const accuracy = res.accuracy
						that.obj.location.latitude = latitude
						that.obj.location.longitude = longitude
					}
				})
			},
			background_location() {
				const that = this
				wx.startLocationUpdateBackground({
					success(res) {
						console.log('开启后台定位', res)
						that.obj.back_location = '成功开启后台定位'
					},
					fail(res) {
						console.log('开启后台定位失败', res)
						that.obj.back_location = '开启后台定位失败' + res.errMsg
					}
				})
			},
			background_audio() {
				const backgroundAudioManager = wx.getBackgroundAudioManager()
				let a = backgroundAudioManager
				
				backgroundAudioManager.title = '此时此刻'
				backgroundAudioManager.epname = '此时此刻'
				backgroundAudioManager.singer = '许巍'
				backgroundAudioManager.coverImgUrl = 'http://y.gtimg.cn/music/photo_new/T002R300x300M000003rsKF44GyaSk.jpg?max_age=2592000'
				if(this.is_pause ==1){
					a.src = 'http://ws.stream.qqmusic.qq.com/C400001Hp44V3pCQ53.m4a?guid=7891022740&vkey=71FFFA442C25CD4A47402CCDE31D4E1679517E7B6E70A907D0FB6F5123F1456DBD342C4EA24066EC4F1A17C8E9E4A8BBC6A2BC1AF4017E34&uin=4608&fromtag=66'
					a.play()
				}
				if(this.is_pause == 2){
					a.pause()
				}
			},
			pause(){
				if(!this.loop){
					console.log('结束播放')
					return;
				}
				console.log('暂停播放音乐,10秒后自动播放')
				this.is_pause = 2
				this.background_audio()
				this.is_pause = 1
			},
			start(){
				if(!this.loop){
					console.log('结束播放')
					return;
				}
				console.log('开始播放音乐,10秒后自动暂停')
				this.is_pause = 1
				this.background_audio()
				setTimeout(()=>{
					this.pause()
				},1000*10)
				setTimeout(()=>{
					this.start()
				},1000*20)
			}


		}
	}
</script>

<style lang="less">
	.content {
		.data {
			margin: 10px;

			.box {
				border: 1px solid #1AAD19;
				padding: 10px;
				margin-bottom: 5px;
				border-radius: 5px;

				.head {
					.name {
						margin-top: 15px;
						margin-left: 30%;
					}

					.img {
						height: 50px;
						width: 50px;
						margin-left: 10px;
					}
				}

				.token {}

				.address {}

				.location {
					.lat {
						display: flex;
						margin-top: 5px;
						justify-content: space-between;

						.lat_1 {}

						.lat_2 {}
					}
				}

				.back_location {}
			}

		}

		.btn {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding-top: 150px;

			.btn_1 {
				width: 300px;
			}
		}

	}
</style>
