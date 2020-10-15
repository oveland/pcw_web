# INIT FUNCTIONS

function log(){
  CURRENT_TIME=$(date +"%r")
  MESSAGE="${CURRENT_TIME}  $1"
  echo ${MESSAGE} >> ${FILE_LOG}
#  echo ${MESSAGE}
}

# INIT SCRIPT

FILE_LOG=/backups/movilidad/log.txt

TABLE=$1
DATE_BACKUP_FROM=$2
DATE_BACKUP_TO=$3

PATH_BACKUP=/backups/movilidad
PATH_BACKUP_DIR=${PATH_BACKUP}/${TABLE}

DB_HOST=localhost
DB_NAME=GPS
DB_PASS=pcw_oveland
DB_USER=oveland
DB_PORT=5433

ZIP_FILE_NAME=${TABLE}.tar.gz
AWS_S3_BUCKET_BACKUP_DATABASE=pcw-backups-database/${DATE_BACKUP_TO//-/_}/


# check path and delete old files

rm -r ${PATH_BACKUP_DIR}
mkdir ${PATH_BACKUP_DIR}
cd ${PATH_BACKUP_DIR}

echo "\n\n**************** $(date +"%Y-%m-%d")  **************** \n" >> ${FILE_LOG}

log " ••• Starting backup process for table: ${TABLE} •••\n"
log "    Exporting table ${TABLE}..."
PGPASSWORD="${DB_PASS}" pg_dump -h ${DB_HOST} -d ${DB_NAME} -U ${DB_USER} -p ${DB_PORT} -a --data-only --column-inserts -t ${TABLE} > ${TABLE}.sql

log "    Compressing sql file..."
rm ${PATH_BACKUP}/${ZIP_FILE_NAME}
sleep 1
tar -cvzf ${PATH_BACKUP}/${ZIP_FILE_NAME} ${PATH_BACKUP_DIR}/

log "    Moving backup to AWS S3. Bucket ${AWS_S3_BUCKET_BACKUP_DATABASE}${ZIP_FILE_NAME}\n"
aws s3 mv ${PATH_BACKUP}/${ZIP_FILE_NAME} s3://${AWS_S3_BUCKET_BACKUP_DATABASE}

log " ••• Backup for table ${TABLE} finished successfully! •••" >> ${FILE_LOG}

log "\n********************************************* \n" >> ${FILE_LOG}

log "Finished successfully at $(date +"%r")!"

echo "true";