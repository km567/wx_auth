import {Api_url} from './config'

//这里引入token的作用是：token失效后自动再次获取token
// #ifdef MP-WEIXIN   
	import {XcxToken} from './xcx_token.js'
	var token = new XcxToken();
// #endif

// #ifdef H5
	import {WxToken} from './wx_token.js'
	var token = new WxToken();
// #endif
 

export default { 
	async post(url, param) {
		const res = await this.uni_request(url,param,'post') 		
		console.log('结果：',res)
		return res;
	},
	async get(url, param) {
		const res = await this.uni_request(url,param,'get')
		console.log('结果：',res)
		return res;
	}, 
	uni_request(url,param,method,again_quest=true) {
		const that=this
	    return new Promise((cback, reject) => {
	    	uni.request({
	    		url: Api_url + url,
	    		data: param,
	    		method:method,
	    		header: {
	    			token:uni.getStorageSync("token")
	    		},
	    	}).then(data => { //data为一个数组，数组第一项为错误信息，第二项为返回数据
	    		var [error, res] = data;
	    		var res_code = res.statusCode.toString(); 
	    		if (res_code.charAt(0) == 2) { 
	    			if(res_code==200){
	    				console.log('200',url)
	    				cback(res.data); 
	    			}else{
						console.log('201',url)
						uni.showToast({
							title:res.data.msg,
							icon:'none'
						})
					}
				}else{
					if(res_code==401){
						//登录失效
						console.log('401',url) 
						if(again_quest){
							token.getTokenFromServer(()=>{
								const again_res=that.uni_request(url,param,method,false)	
								cback(again_res); 
							});				
						}else{
							console.log('再次登陆仍然失败',url)
						} 
					}else{
						console.log('400/500',url)
						uni.showToast({
							title:'请求异常',
							icon:'none'
						})
					}
				}	
			}).catch(err => { 
				console.log('catch:',err);					 
			})
		})	
	}, 

}
