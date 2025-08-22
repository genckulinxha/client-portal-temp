#!/bin/bash

# SSL Setup Script for demo.genckulinxha.com
# This script sets up Certbot SSL certificates for your domain

set -e

DOMAIN="demo.genckulinxha.com"
EMAIL="your-email@example.com"  # Replace with your actual email
WEBROOT="/var/www/certbot"

echo "🔐 Setting up SSL for $DOMAIN"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run this script as root or with sudo${NC}"
    exit 1
fi

# Step 1: Install Certbot if not already installed
echo -e "${YELLOW}Step 1: Checking Certbot installation...${NC}"
if ! command -v certbot &> /dev/null; then
    echo "Installing Certbot..."
    
    # Detect OS and install accordingly
    if [ -f /etc/debian_version ]; then
        # Debian/Ubuntu
        apt update
        apt install -y certbot python3-certbot-nginx
    elif [ -f /etc/redhat-release ]; then
        # RHEL/CentOS/Rocky
        dnf install -y certbot python3-certbot-nginx
    else
        echo -e "${RED}Unsupported OS. Please install Certbot manually.${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}Certbot is already installed${NC}"
fi

# Step 2: Create webroot directory
echo -e "${YELLOW}Step 2: Creating webroot directory...${NC}"
mkdir -p $WEBROOT
chown -R www-data:www-data $WEBROOT 2>/dev/null || chown -R nginx:nginx $WEBROOT

# Step 3: Test Nginx configuration
echo -e "${YELLOW}Step 3: Testing Nginx configuration...${NC}"
nginx -t
if [ $? -ne 0 ]; then
    echo -e "${RED}Nginx configuration test failed. Please fix the configuration first.${NC}"
    exit 1
fi

# Step 4: Reload Nginx
echo -e "${YELLOW}Step 4: Reloading Nginx...${NC}"
systemctl reload nginx

# Step 5: Obtain SSL certificate
echo -e "${YELLOW}Step 5: Obtaining SSL certificate...${NC}"
certbot certonly \
    --webroot \
    --webroot-path=$WEBROOT \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email \
    --force-renewal \
    -d $DOMAIN

if [ $? -eq 0 ]; then
    echo -e "${GREEN}SSL certificate obtained successfully!${NC}"
else
    echo -e "${RED}Failed to obtain SSL certificate${NC}"
    exit 1
fi

# Step 6: Update Nginx configuration to use SSL
echo -e "${YELLOW}Step 6: Testing SSL configuration...${NC}"
nginx -t
if [ $? -eq 0 ]; then
    systemctl reload nginx
    echo -e "${GREEN}Nginx reloaded with SSL configuration${NC}"
else
    echo -e "${RED}SSL configuration test failed${NC}"
    exit 1
fi

# Step 7: Setup auto-renewal
echo -e "${YELLOW}Step 7: Setting up auto-renewal...${NC}"
# Test renewal
certbot renew --dry-run

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Auto-renewal test successful${NC}"
    
    # Add cron job for auto-renewal if not exists
    CRON_JOB="0 12 * * * /usr/bin/certbot renew --quiet && /usr/bin/systemctl reload nginx"
    (crontab -l 2>/dev/null | grep -v "/usr/bin/certbot renew"; echo "$CRON_JOB") | crontab -
    echo -e "${GREEN}Auto-renewal cron job added${NC}"
else
    echo -e "${YELLOW}Auto-renewal test failed, but SSL is still configured${NC}"
fi

echo ""
echo -e "${GREEN}🎉 SSL setup complete!${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Update the EMAIL variable in this script to your actual email"
echo "2. Make sure your domain DNS points to this server"
echo "3. Test your site: https://$DOMAIN"
echo "4. Check SSL rating: https://www.ssllabs.com/ssltest/analyze.html?d=$DOMAIN"
echo ""
echo -e "${YELLOW}Certificate locations:${NC}"
echo "Certificate: /etc/letsencrypt/live/$DOMAIN/fullchain.pem"
echo "Private Key: /etc/letsencrypt/live/$DOMAIN/privkey.pem"