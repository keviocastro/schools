FROM keviocastro/laravel:5

# For aws environment test: "gennin"
ENV DB_HOST="aahmh4goognvs1.cxudyan7umuk.us-east-1.rds.amazonaws.com"
ENV DB_USERNAME="schools"
ENV DB_PASSWORD="secretone"

EXPOSE 80