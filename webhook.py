# webhook_server.py
from flask import Flask, request
import hmac
import hashlib
import os
import subprocess

app = Flask(__name__)

GITHUB_SECRET = b'wolfheluo'  # 從 GitHub Webhook 設定頁拿到的

def verify_signature(payload, signature):
    mac = hmac.new(GITHUB_SECRET, msg=payload, digestmod=hashlib.sha256)
    return hmac.compare_digest('sha256=' + mac.hexdigest(), signature)

@app.route('/webhook', methods=['POST'], strict_slashes=False)
def webhook():
    signature = request.headers.get('X-Hub-Signature-256')
    if not signature or not verify_signature(request.data, signature):
        return 'Signature verification failed', 400
    
    payload = request.json
    repo_name = payload['repository']['full_name']  # e.g. "user/project1"

    if repo_name == 'user/project1':
        subprocess.Popen(['bash', '-c', 'cd /path/to/project1 && git fetch origin && git reset --hard origin/main'])
    elif repo_name == 'user/project2':
        subprocess.Popen(['bash', '-c', 'cd /path/to/project2 && git fetch origin && git reset --hard origin/main'])
    else:
        return 'Unknown repository', 400
    
    return 'OK', 200


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=9000)
