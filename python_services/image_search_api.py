from flask import Flask, request, jsonify
import imagehash
from PIL import Image
import os

app = Flask(__name__)

@app.route('/hash', methods=['GET', 'POST'])
def generate_hash():
    # Menerima path gambar dari parameter URL atau form data
    if request.method == 'GET':
        image_path = request.args.get('path')
    else:
        # Untuk antisipasi jika path dikirim via JSON / form-data di masa depan
        image_path = request.form.get('path') or request.json.get('path')
    
    if not image_path:
        return jsonify({"error": "Missing 'path' parameter"}), 400
    
    if not os.path.exists(image_path):
        return jsonify({"error": "File not found on server"}), 404
        
    try:
        # Buka gambar dan buat perceptual hash
        img = Image.open(image_path)
        # Menggunakan pHash dengan ukuran default 8 (menghasilkan hash 64-bit / 16 karakter hex)
        hash_val = str(imagehash.phash(img))
        
        return jsonify({
            "status": "success",
            "hash": hash_val
        }), 200
        
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    # Berjalan di localhost port 5000
    app.run(host='127.0.0.1', port=5000, debug=False)
