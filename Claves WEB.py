import customtkinter as ctk
import mysql.connector
from tkinter import messagebox
from PIL import Image

# Configuración de la conexión a la base de datos
db = mysql.connector.connect(
    host="sql101.byethost24.com",
    user="b24_37825174",
    password="T!/giK9e9mch4?E",
    database="b24_37825174_Dinamize"
)

cursor = db.cursor()

def vender_clave(clave_id):
    confirmacion = messagebox.askyesno("Confirmar Venta", "¿Se realizó la venta?")
    if confirmacion:
        try:
            cursor.execute("UPDATE Claves_de_activacion SET Estado = 'Vendida' WHERE id = %s", (clave_id,))
            db.commit()
            cargar_claves()
            messagebox.showinfo("Éxito", "Clave vendida correctamente.")
        except Exception as e:
            messagebox.showerror("Error", str(e))
    else:
        # No hacer nada si el usuario cancela
        pass

def cargar_claves():
    for widget in frame_claves.winfo_children():
        widget.destroy()
    cursor.execute("SELECT id, clave FROM Claves_de_activacion WHERE Estado = 'Sin Asignar'")
    claves = cursor.fetchall()
    for clave in claves:
        frame_item = ctk.CTkFrame(frame_claves)
        frame_item.pack(fill="x", padx=10, pady=5)
        
        lbl = ctk.CTkLabel(frame_item, text=clave[1])
        lbl.pack(side="left", padx=10)
        
        btn = ctk.CTkButton(frame_item, text="Vender Clave", command=lambda cid=clave[0]: vender_clave(cid))
        btn.pack(side="right", padx=10)

app = ctk.CTk()
app.title("Dinamize Kiosk Keys")
app.geometry("300x400")

# Establecer el icono de la ventana
try:
    app.iconbitmap("img/logo.ico")
except Exception as e:
    messagebox.showerror("Error", f"No se pudo cargar el icono: {e}")

frame_claves = ctk.CTkScrollableFrame(app)
frame_claves.pack(pady=10, padx=20, fill="both", expand=True)

cargar_claves()

app.mainloop()