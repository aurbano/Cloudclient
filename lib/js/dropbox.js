/**
 * Dropbox JS API
 * Pure JavaScript Dropbox API written by Alejandro U. Alvarez
 * there were other solutions available at the time, but they were all too
 * complex for the requirements I had.
 */

var Dropbox = {
	// API key encoded with https://dl-web.dropbox.com/spa/pjlfdak1tmznswp/api_keys.js/public/index.html
	//key : 'w4+RfvRJjEA=|QmwdFN6Ii5SleISvcRVBMwrcKe2cwI/Mn2M4WlVXGg==',
	key : 'l3hoya7no44ixlt',
	requestToken : function(){
		$.post('https://api.dropbox.com/1/oauth/request_token',{oauth_consumer_key : this.key, oauth_consumer_secret : '5wrlhca58get4qx', oauth_signature_method : 'PLAINTEXT', oauth_signature : 'ignored', oauth_nonce : this.nonce(), oauth_timestamp : this.timestamp()},function(data){
			alert(data)
		});
	},
	timestamp : function timestamp() {
        var t = (new Date()).getTime();
        return Math.floor(t / 1000);
    },
	nonce : function nonce(length) {
		// Generate unique string
        var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz';
        var result = "";
        for (var i = 0; i < chars.length; ++i) {
            var rnum = Math.floor(Math.random() * chars.length);
            result += chars.substring(rnum, rnum+1);
        }
        return result;
    },
	getAuthorizeUrl : function(){
		
	}
};