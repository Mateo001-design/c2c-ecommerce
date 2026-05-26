"""Generate all design diagrams for iTradeZA C2C E-Commerce Platform"""
import graphviz
import os

OUTPUT_DIR = os.path.dirname(os.path.abspath(__file__))

def create_eerd():
    """Enhanced Entity Relationship Diagram"""
    dot = graphviz.Digraph('EERD', format='png')
    dot.attr(rankdir='TB', size='12,16', dpi='150')
    dot.attr('node', shape='record', fontname='Arial', fontsize='10')
    dot.attr('edge', fontname='Arial', fontsize='9')

    # Entities
    dot.node('roles', '{roles|id (PK)\lname\ldescription\lcreated_at\l}')
    dot.node('users', '{users|id (PK)\lusername\lemail\lpassword\lfirst_name\llast_name\lphone\lcity\lprovince\lrole_id (FK)\lis_verified\lcreated_at\l}')
    dot.node('categories', '{categories|id (PK)\lname\licon\l}')
    dot.node('products', '{products|id (PK)\lseller_id (FK)\lcategory_id (FK)\ltitle\ldescription\lprice\lcondition\lquantity\llocation\lstatus\lcreated_at\l}')
    dot.node('product_images', '{product_images|id (PK)\lproduct_id (FK)\limage_path\lis_primary\l}')
    dot.node('cart', '{cart|id (PK)\luser_id (FK)\lproduct_id (FK)\lquantity\l}')
    dot.node('orders', '{orders|id (PK)\lbuyer_id (FK)\lseller_id (FK)\ltotal_amount\lstatus\lpayment_method\lshipping_address\lcreated_at\l}')
    dot.node('order_items', '{order_items|id (PK)\lorder_id (FK)\lproduct_id (FK)\lquantity\lprice\l}')
    dot.node('messages', '{messages|id (PK)\lsender_id (FK)\lreceiver_id (FK)\lproduct_id (FK)\lmessage\lis_read\lcreated_at\l}')
    dot.node('reviews', '{reviews|id (PK)\lorder_id (FK)\lreviewer_id (FK)\lseller_id (FK)\lrating\lcomment\lcreated_at\l}')

    # Relationships
    dot.edge('roles', 'users', label='1:N\lhas', arrowhead='crow')
    dot.edge('users', 'products', label='1:N\llists', arrowhead='crow')
    dot.edge('categories', 'products', label='1:N\lcontains', arrowhead='crow')
    dot.edge('products', 'product_images', label='1:N\lhas', arrowhead='crow')
    dot.edge('users', 'cart', label='1:N\lowns', arrowhead='crow')
    dot.edge('products', 'cart', label='1:N\lin', arrowhead='crow')
    dot.edge('users', 'orders', label='1:N\lplaces', arrowhead='crow', style='solid')
    dot.edge('orders', 'order_items', label='1:N\lcontains', arrowhead='crow')
    dot.edge('products', 'order_items', label='1:N\lin', arrowhead='crow')
    dot.edge('users', 'messages', label='1:N\lsends', arrowhead='crow')
    dot.edge('orders', 'reviews', label='1:1\lhas', arrowhead='normal')
    dot.edge('users', 'reviews', label='1:N\lreceives', arrowhead='crow', style='dashed')

    dot.render(os.path.join(OUTPUT_DIR, 'eerd'), cleanup=True)
    print("✓ EERD generated")


def create_context_diagram():
    """Context Diagram (DFD Level 0)"""
    dot = graphviz.Digraph('Context', format='png')
    dot.attr(rankdir='LR', size='10,6', dpi='150')
    dot.attr('node', fontname='Arial', fontsize='11')

    # External entities (rectangles)
    dot.node('buyer', 'Buyer', shape='box', style='filled', fillcolor='#d4edda')
    dot.node('seller', 'Seller', shape='box', style='filled', fillcolor='#d4edda')
    dot.node('admin', 'Administrator', shape='box', style='filled', fillcolor='#d4edda')
    dot.node('payment', 'Payment\nGateway', shape='box', style='filled', fillcolor='#fff3cd')

    # System (circle)
    dot.node('system', 'iTradeZA\nC2C Platform', shape='circle', style='filled',
             fillcolor='#cce5ff', width='2.5', height='2.5', fontsize='12')

    # Data flows
    dot.edge('buyer', 'system', label='Registration, Orders,\nMessages, Reviews')
    dot.edge('system', 'buyer', label='Product Listings,\nOrder Status, Notifications')
    dot.edge('seller', 'system', label='Product Listings,\nOrder Updates')
    dot.edge('system', 'seller', label='Orders Received,\nMessages, Payments')
    dot.edge('admin', 'system', label='User Management,\nRole Assignment')
    dot.edge('system', 'admin', label='Reports, Stats,\nUser Data')
    dot.edge('system', 'payment', label='Payment Requests')
    dot.edge('payment', 'system', label='Payment Confirmations')

    dot.render(os.path.join(OUTPUT_DIR, 'context_diagram'), cleanup=True)
    print("✓ Context Diagram generated")


def create_dfd():
    """Data Flow Diagram (Level 1)"""
    dot = graphviz.Digraph('DFD', format='png')
    dot.attr(rankdir='TB', size='14,10', dpi='150')
    dot.attr('node', fontname='Arial', fontsize='10')

    # External entities
    dot.node('buyer', 'Buyer', shape='box', style='filled', fillcolor='#d4edda')
    dot.node('seller', 'Seller', shape='box', style='filled', fillcolor='#d4edda')
    dot.node('admin', 'Admin', shape='box', style='filled', fillcolor='#d4edda')

    # Processes (circles)
    dot.node('p1', '1.0\nUser\nRegistration', shape='circle', style='filled', fillcolor='#cce5ff')
    dot.node('p2', '2.0\nProduct\nManagement', shape='circle', style='filled', fillcolor='#cce5ff')
    dot.node('p3', '3.0\nOrder\nProcessing', shape='circle', style='filled', fillcolor='#cce5ff')
    dot.node('p4', '4.0\nMessaging', shape='circle', style='filled', fillcolor='#cce5ff')
    dot.node('p5', '5.0\nAdmin\nManagement', shape='circle', style='filled', fillcolor='#cce5ff')
    dot.node('p6', '6.0\nReview\nSystem', shape='circle', style='filled', fillcolor='#cce5ff')

    # Data stores (open-ended rectangles approximated)
    dot.node('d1', 'D1: Users', shape='box', style='filled', fillcolor='#f8f9fa')
    dot.node('d2', 'D2: Products', shape='box', style='filled', fillcolor='#f8f9fa')
    dot.node('d3', 'D3: Orders', shape='box', style='filled', fillcolor='#f8f9fa')
    dot.node('d4', 'D4: Messages', shape='box', style='filled', fillcolor='#f8f9fa')
    dot.node('d5', 'D5: Reviews', shape='box', style='filled', fillcolor='#f8f9fa')

    # Flows
    dot.edge('buyer', 'p1', label='Registration Data')
    dot.edge('seller', 'p1', label='Registration Data')
    dot.edge('p1', 'd1', label='User Record')

    dot.edge('seller', 'p2', label='Product Details')
    dot.edge('p2', 'd2', label='Product Record')
    dot.edge('d2', 'buyer', label='Product Listings')

    dot.edge('buyer', 'p3', label='Order Request')
    dot.edge('d2', 'p3', label='Product Info')
    dot.edge('p3', 'd3', label='Order Record')
    dot.edge('d3', 'seller', label='Order Notification')

    dot.edge('buyer', 'p4', label='Message')
    dot.edge('seller', 'p4', label='Reply')
    dot.edge('p4', 'd4', label='Message Record')

    dot.edge('admin', 'p5', label='Management Actions')
    dot.edge('d1', 'p5', label='User Data')
    dot.edge('p5', 'd1', label='Updated Users/Roles')

    dot.edge('buyer', 'p6', label='Rating & Comment')
    dot.edge('p6', 'd5', label='Review Record')

    dot.render(os.path.join(OUTPUT_DIR, 'dfd_level1'), cleanup=True)
    print("✓ DFD Level 1 generated")


def create_use_case():
    """Use Case Diagram"""
    dot = graphviz.Digraph('UseCase', format='png')
    dot.attr(rankdir='LR', size='12,10', dpi='150')
    dot.attr('node', fontname='Arial', fontsize='10')

    # Actors (stick figures approximated as boxes)
    dot.node('buyer', '👤 Buyer', shape='box', style='filled', fillcolor='#d4edda')
    dot.node('seller', '👤 Seller', shape='box', style='filled', fillcolor='#d4edda')
    dot.node('admin', '👤 Admin', shape='box', style='filled', fillcolor='#f8d7da')

    # System boundary
    with dot.subgraph(name='cluster_system') as c:
        c.attr(label='iTradeZA C2C Platform', style='dashed', color='#333')

        # Use cases (ellipses)
        c.node('uc1', 'Register Account', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc2', 'Login/Logout', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc3', 'Browse Products', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc4', 'Search & Filter', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc5', 'Add to Cart', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc6', 'Checkout & Pay', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc7', 'Track Orders', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc8', 'Send Message', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc9', 'Leave Review', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc10', 'List Product', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc11', 'Manage Listings', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc12', 'Update Order Status', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc13', 'Manage Users (CRUD)', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc14', 'Manage Roles (RBAC)', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc15', 'View Dashboard/Reports', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc16', 'Manage Products', shape='ellipse', style='filled', fillcolor='#fff')
        c.node('uc17', 'Manage Orders', shape='ellipse', style='filled', fillcolor='#fff')

    # Buyer associations
    dot.edge('buyer', 'uc1'); dot.edge('buyer', 'uc2')
    dot.edge('buyer', 'uc3'); dot.edge('buyer', 'uc4')
    dot.edge('buyer', 'uc5'); dot.edge('buyer', 'uc6')
    dot.edge('buyer', 'uc7'); dot.edge('buyer', 'uc8')
    dot.edge('buyer', 'uc9')

    # Seller associations
    dot.edge('seller', 'uc1'); dot.edge('seller', 'uc2')
    dot.edge('seller', 'uc10'); dot.edge('seller', 'uc11')
    dot.edge('seller', 'uc12'); dot.edge('seller', 'uc8')

    # Admin associations
    dot.edge('admin', 'uc2'); dot.edge('admin', 'uc13')
    dot.edge('admin', 'uc14'); dot.edge('admin', 'uc15')
    dot.edge('admin', 'uc16'); dot.edge('admin', 'uc17')

    dot.render(os.path.join(OUTPUT_DIR, 'use_case'), cleanup=True)
    print("✓ Use Case Diagram generated")


def create_db_schema():
    """Database Schema Diagram"""
    dot = graphviz.Digraph('Schema', format='png')
    dot.attr(rankdir='LR', size='16,10', dpi='150')
    dot.attr('node', shape='record', fontname='Courier', fontsize='9')
    dot.attr('edge', fontname='Arial', fontsize='8')

    dot.node('roles', '<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0"><TR><TD BGCOLOR="#28a745" COLSPAN="2"><FONT COLOR="white"><B>roles</B></FONT></TD></TR><TR><TD>🔑 id</TD><TD>INT PK AI</TD></TR><TR><TD>name</TD><TD>VARCHAR(50)</TD></TR><TR><TD>description</TD><TD>TEXT</TD></TR><TR><TD>created_at</TD><TD>TIMESTAMP</TD></TR></TABLE>>', shape='none')

    dot.node('users', '<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0"><TR><TD BGCOLOR="#28a745" COLSPAN="2"><FONT COLOR="white"><B>users</B></FONT></TD></TR><TR><TD>🔑 id</TD><TD>INT PK AI</TD></TR><TR><TD>username</TD><TD>VARCHAR(50) UQ</TD></TR><TR><TD>email</TD><TD>VARCHAR(100) UQ</TD></TR><TR><TD>password</TD><TD>VARCHAR(255)</TD></TR><TR><TD>first_name</TD><TD>VARCHAR(50)</TD></TR><TR><TD>last_name</TD><TD>VARCHAR(50)</TD></TR><TR><TD>phone</TD><TD>VARCHAR(20)</TD></TR><TR><TD>city</TD><TD>VARCHAR(100)</TD></TR><TR><TD>province</TD><TD>VARCHAR(50)</TD></TR><TR><TD>🔗 role_id</TD><TD>INT FK</TD></TR><TR><TD>is_verified</TD><TD>BOOLEAN</TD></TR><TR><TD>created_at</TD><TD>TIMESTAMP</TD></TR></TABLE>>', shape='none')

    dot.node('products', '<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0"><TR><TD BGCOLOR="#28a745" COLSPAN="2"><FONT COLOR="white"><B>products</B></FONT></TD></TR><TR><TD>🔑 id</TD><TD>INT PK AI</TD></TR><TR><TD>🔗 seller_id</TD><TD>INT FK</TD></TR><TR><TD>🔗 category_id</TD><TD>INT FK</TD></TR><TR><TD>title</TD><TD>VARCHAR(200)</TD></TR><TR><TD>description</TD><TD>TEXT</TD></TR><TR><TD>price</TD><TD>DECIMAL(10,2)</TD></TR><TR><TD>condition</TD><TD>ENUM</TD></TR><TR><TD>quantity</TD><TD>INT</TD></TR><TR><TD>location</TD><TD>VARCHAR(200)</TD></TR><TR><TD>status</TD><TD>ENUM</TD></TR><TR><TD>created_at</TD><TD>TIMESTAMP</TD></TR></TABLE>>', shape='none')

    dot.node('orders', '<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0"><TR><TD BGCOLOR="#28a745" COLSPAN="2"><FONT COLOR="white"><B>orders</B></FONT></TD></TR><TR><TD>🔑 id</TD><TD>INT PK AI</TD></TR><TR><TD>🔗 buyer_id</TD><TD>INT FK</TD></TR><TR><TD>🔗 seller_id</TD><TD>INT FK</TD></TR><TR><TD>total_amount</TD><TD>DECIMAL(10,2)</TD></TR><TR><TD>status</TD><TD>ENUM</TD></TR><TR><TD>payment_method</TD><TD>VARCHAR(50)</TD></TR><TR><TD>shipping_address</TD><TD>TEXT</TD></TR><TR><TD>created_at</TD><TD>TIMESTAMP</TD></TR></TABLE>>', shape='none')

    dot.node('messages', '<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0"><TR><TD BGCOLOR="#28a745" COLSPAN="2"><FONT COLOR="white"><B>messages</B></FONT></TD></TR><TR><TD>🔑 id</TD><TD>INT PK AI</TD></TR><TR><TD>🔗 sender_id</TD><TD>INT FK</TD></TR><TR><TD>🔗 receiver_id</TD><TD>INT FK</TD></TR><TR><TD>🔗 product_id</TD><TD>INT FK NULL</TD></TR><TR><TD>message</TD><TD>TEXT</TD></TR><TR><TD>is_read</TD><TD>BOOLEAN</TD></TR><TR><TD>created_at</TD><TD>TIMESTAMP</TD></TR></TABLE>>', shape='none')

    # Relationships
    dot.edge('roles', 'users', label='1:N')
    dot.edge('users', 'products', label='1:N')
    dot.edge('users', 'orders', label='1:N')
    dot.edge('products', 'orders', label='via order_items', style='dashed')
    dot.edge('users', 'messages', label='1:N')

    dot.render(os.path.join(OUTPUT_DIR, 'db_schema'), cleanup=True)
    print("✓ Database Schema generated")


def create_crc_cards():
    """CRC Cards as a visual diagram"""
    dot = graphviz.Digraph('CRC', format='png')
    dot.attr(rankdir='TB', size='14,16', dpi='150')
    dot.attr('node', shape='none', fontname='Arial', fontsize='10')

    cards = [
        ('User', 'Register account\lLogin/Logout\lUpdate profile\lManage cart\lPlace orders\lSend messages\lLeave reviews\l',
         'Role\lProduct\lCart\lOrder\lMessage\lReview\l'),
        ('Product', 'Store listing details\lManage images\lTrack quantity\lFilter by category\lSearch functionality\l',
         'User (seller)\lCategory\lProductImage\lCart\lOrderItem\l'),
        ('Order', 'Process checkout\lTrack status\lCalculate totals\lStore shipping info\l',
         'User (buyer)\lUser (seller)\lOrderItem\lProduct\lReview\l'),
        ('Cart', 'Add/remove items\lUpdate quantities\lCalculate subtotal\lConvert to order\l',
         'User\lProduct\l'),
        ('Message', 'Send message\lMark as read\lList conversations\lLink to product\l',
         'User (sender)\lUser (receiver)\lProduct\l'),
        ('Admin', 'Manage users (CRUD)\lManage roles (RBAC)\lView dashboard\lManage products\lManage orders\l',
         'User\lRole\lProduct\lOrder\l'),
        ('Role', 'Define permissions\lAssign to users\lRBAC enforcement\l',
         'User\lAdmin\l'),
        ('Review', 'Submit rating\lStore comment\lLink to order\l',
         'User (reviewer)\lUser (seller)\lOrder\l'),
    ]

    for name, resp, collab in cards:
        label = f'''<<TABLE BORDER="1" CELLBORDER="1" CELLSPACING="0" CELLPADDING="4">
        <TR><TD COLSPAN="2" BGCOLOR="#28a745"><FONT COLOR="white"><B>{name}</B></FONT></TD></TR>
        <TR><TD BGCOLOR="#f8f9fa"><B>Responsibilities</B></TD><TD BGCOLOR="#f8f9fa"><B>Collaborators</B></TD></TR>
        <TR><TD ALIGN="LEFT">{resp}</TD><TD ALIGN="LEFT">{collab}</TD></TR>
        </TABLE>>'''
        dot.node(name, label)

    dot.render(os.path.join(OUTPUT_DIR, 'crc_cards'), cleanup=True)
    print("✓ CRC Cards generated")


if __name__ == '__main__':
    print("Generating iTradeZA design diagrams...")
    create_eerd()
    create_context_diagram()
    create_dfd()
    create_use_case()
    create_db_schema()
    create_crc_cards()
    print("\nAll diagrams generated in:", OUTPUT_DIR)
