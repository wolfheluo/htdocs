import os
import time
import requests
import schedule
from datetime import datetime
from pathlib import Path
import urllib3

# 禁用 SSL 警告（因為使用自簽證書）
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

# 配置
DB_URL = "https://34.81.253.201:8888/down/Jag1qyzIG801.db"
BACKUP_DIR = "Backup-DB"
MAX_BACKUPS = 100

def ensure_backup_dir():
    """確保備份目錄存在"""
    Path(BACKUP_DIR).mkdir(exist_ok=True)
    print(f"備份目錄: {BACKUP_DIR}")

def download_database():
    """下載資料庫檔案"""
    try:
        print(f"\n[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] 開始下載資料庫...")
        
        # 生成備份檔案名稱（包含時間戳）
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        backup_filename = f"backup_{timestamp}.db"
        backup_path = os.path.join(BACKUP_DIR, backup_filename)
        
        # 下載檔案（禁用 SSL 驗證）
        response = requests.get(DB_URL, verify=False, timeout=300)
        response.raise_for_status()
        
        # 保存檔案
        with open(backup_path, 'wb') as f:
            f.write(response.content)
        
        file_size = os.path.getsize(backup_path) / 1024 / 1024  # MB
        print(f"✓ 下載完成: {backup_filename} ({file_size:.2f} MB)")
        
        return True
    except Exception as e:
        print(f"✗ 下載失敗: {e}")
        return False

def clean_old_backups():
    """清理舊的備份檔案，只保留最新的 MAX_BACKUPS 個"""
    try:
        # 獲取所有備份檔案
        backup_files = []
        for file in os.listdir(BACKUP_DIR):
            if file.startswith('backup_') and file.endswith('.db'):
                file_path = os.path.join(BACKUP_DIR, file)
                # 獲取檔案的修改時間
                mtime = os.path.getmtime(file_path)
                backup_files.append((file_path, mtime))
        
        # 按時間排序（舊到新）
        backup_files.sort(key=lambda x: x[1])
        
        # 計算需要刪除的檔案數量
        files_to_delete = len(backup_files) - MAX_BACKUPS
        
        if files_to_delete > 0:
            print(f"\n清理舊備份: 目前有 {len(backup_files)} 個備份，將刪除 {files_to_delete} 個最舊的備份...")
            
            # 刪除最舊的檔案
            for i in range(files_to_delete):
                file_path = backup_files[i][0]
                os.remove(file_path)
                print(f"✓ 已刪除: {os.path.basename(file_path)}")
            
            print(f"✓ 清理完成，目前保留 {MAX_BACKUPS} 個備份")
        else:
            print(f"✓ 目前有 {len(backup_files)} 個備份，無需清理")
    
    except Exception as e:
        print(f"✗ 清理失敗: {e}")

def backup_job():
    """執行備份任務"""
    print("\n" + "="*60)
    print("執行資料庫備份任務")
    print("="*60)
    
    # 下載資料庫
    if download_database():
        # 清理舊備份
        clean_old_backups()
    
    print("="*60)
    print(f"下次備份時間: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')} 後的 12 小時")
    print("="*60)

def main():
    """主程式"""
    print("="*60)
    print("資料庫自動備份系統")
    print("="*60)
    print(f"資料庫來源: {DB_URL}")
    print(f"備份目錄: {BACKUP_DIR}")
    print(f"保留數量: {MAX_BACKUPS} 個備份")
    print(f"備份頻率: 每 12 小時")
    print("="*60)
    
    # 確保備份目錄存在
    ensure_backup_dir()
    
    # 立即執行一次備份
    print("\n立即執行首次備份...")
    backup_job()
    
    # 設定每 12 小時執行一次
    schedule.every(12).hours.do(backup_job)
    
    print("\n✓ 排程已啟動，程式將持續運行...")
    print("按 Ctrl+C 可停止程式\n")
    
    # 持續運行
    try:
        while True:
            schedule.run_pending()
            time.sleep(60)  # 每分鐘檢查一次
    except KeyboardInterrupt:
        print("\n\n程式已停止")

if __name__ == "__main__":
    main()
