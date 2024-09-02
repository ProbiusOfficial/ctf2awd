#!/bin/sh
set -e

# Remove the script itself for security reasons
rm -f /docker-entrypoint.sh

# Get the user (assuming the user directory exists in /home)
user=$(ls /home)

# Check the environment variables for the flag and assign to INSERT_FLAG
if [ "$DASFLAG" ]; then
    INSERT_FLAG="$DASFLAG"
    export DASFLAG=no_FLAG
    DASFLAG=no_FLAG
elif [ "$FLAG" ]; then
    INSERT_FLAG="$FLAG"
    export FLAG=no_FLAG
    FLAG=no_FLAG
elif [ "$GZCTF_FLAG" ]; then
    INSERT_FLAG="$GZCTF_FLAG"
    export GZCTF_FLAG=no_FLAG
    GZCTF_FLAG=no_FLAG
else
    INSERT_FLAG="flag{TEST_Dynamic_FLAG}"
fi

# Write the flag to a file (update path as needed)
echo $INSERT_FLAG | tee /flag

# Set permissions for the flag file
chmod 744 /flag

# Start SSH service
service ssh start

echo "SSH service started. Waiting for user actions..."

# Run a loop to keep the container running
while true; do
    sleep 60
done
