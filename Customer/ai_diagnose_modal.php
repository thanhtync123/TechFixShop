<?php

?>

<style>
    .ai-diagnose-container {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 20px;
        text-align: center;
        color: #333;
    }
    .ai-diagnose-container h3 {
        color: #007bff;
        margin-bottom: 10px;
        font-size: 1.8em;
    }
    .ai-diagnose-container p {
        font-size: 1.1em;
        margin-bottom: 25px;
        color: #555;
    }
    .upload-area {
        border: 2px dashed #ccc;
        border-radius: 10px;
        padding: 30px;
        margin: 20px auto;
        max-width: 600px;
        cursor: pointer;
        transition: border-color 0.3s ease;
        background-color: #f9f9f9;
    }
    .upload-area:hover {
        border-color: #007bff;
    }
    .upload-area input[type="file"] {
        display: none;
    }
    .upload-icon {
        font-size: 3em;
        color: #007bff;
        margin-bottom: 10px;
    }
    .upload-text {
        font-size: 1.2em;
        color: #666;
        font-weight: 600;
    }
    .preview-area {
        margin-top: 20px;
        display: none; 
    }
    .preview-area img, .preview-area video {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .diagnose-button {
        background-color: #28a745;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        font-size: 1.2em;
        cursor: pointer;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }
    .diagnose-button:hover {
        background-color: #218838;
    }
    .diagnose-button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    .diagnosis-result {
        margin-top: 30px;
        padding: 20px;
        background-color: #e6f7ff;
        border: 1px solid #91d5ff;
        border-radius: 8px;
        font-size: 1.1em;
        color: #0056b3;
        display: none; 
        text-align: left; 
    }
    .diagnosis-result strong {
        color: #003a70;
    }
    .select-service-button {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        cursor: pointer;
        margin-top: 15px;
        transition: background-color 0.3s ease;
    }
    .select-service-button:hover {
        background-color: #0056b3;
    }
    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
        display: none; 
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="ai-diagnose-container">
    <h3>Bạn không chắc mình cần dịch vụ gì?</h3>
    <p>Hãy để AI giúp bạn chẩn đoán lỗi qua hình ảnh hoặc video!</p>

    <input type="file" id="aiFileUpload" accept="image/*,video/*" />

    <div class="upload-area" id="uploadArea">
        <div class="upload-icon">⬆️</div>
        <div class="upload-text">Tải ảnh/video lỗi của bạn lên đây</div>
    </div>

    <div class="preview-area" id="previewArea">
    </div>

    <button type="button" class="diagnose-button" id="diagnoseButton" disabled>
        💡 Chẩn đoán ngay
    </button>
    <div class="loading-spinner" id="loadingSpinner"></div>

    <div class="diagnosis-result" id="diagnosisResult">
    </div>
</div>